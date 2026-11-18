<?php

namespace App\Http\Controllers\Clients;

use App\Helpers\CategoryHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Clients\ShopController;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Cart;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductSlugHistory;
use App\Models\PopupContent;
use App\Models\SupportStaff;
use App\Models\Tag;
use App\Models\Voucher;
use App\Services\ProductViewService;
use App\Jobs\RecordProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(
        private ProductViewService $productViewService
    ) {}

    public function detail($slug)
    {
        // Tối ưu: Cache cả product và category check để tránh query không cần thiết
        // Key cache: 'slug_type_' để phân biệt product/category/not_found
        $cacheKey = 'slug_type_'.$slug;
        
        try {
            // Dùng slug_index để resolve 1 phát, không phải check tuần tự
            // Cache 1 giờ, fallback sang UNION ALL cũ nếu chưa có dữ liệu slug_index (đảm bảo không 404 oan)
            $slugType = Cache::remember($cacheKey, 3600, function () use ($slug) {
                $index = \App\Models\SlugIndex::where('slug', $slug)
                    ->where('is_active', true)
                    ->select('type', 'entity_id', 'target_slug')
                    ->first();

                if ($index) {
                    return [
                        'type' => $index->type,
                        'entity_id' => $index->entity_id,
                        'target_slug' => $index->target_slug,
                    ];
                }

                // Fallback: UNION ALL để không gián đoạn khi slug_index chưa được seed đầy đủ
                $sql = "
                    (
                        SELECT id, 'product' AS type, NULL AS target_slug
                        FROM products
                        WHERE slug = ? AND is_active = 1
                        LIMIT 1
                    )
                    UNION ALL
                    (
                        SELECT id, 'post' AS type, NULL AS target_slug
                        FROM posts
                        WHERE slug = ? AND status = 'published'
                        LIMIT 1
                    )
                    UNION ALL
                    (
                        SELECT id, 'category' AS type, NULL AS target_slug
                        FROM categories
                        WHERE slug = ? AND is_active = 1
                        LIMIT 1
                    )
                    LIMIT 1
                ";

                $result = DB::selectOne($sql, [$slug, $slug, $slug]);

                if (! $result) {
                    return ['type' => 'not_found', 'entity_id' => null, 'target_slug' => null];
                }

                return [
                    'type' => $result->type ?? 'not_found',
                    'entity_id' => $result->id ?? null,
                    'target_slug' => $result->target_slug ?? null,
                ];
            });
            
            // Nếu là bài viết, redirect sang route bài viết chuẩn
            if ($slugType['type'] === 'post') {
                // Không load thêm gì, chỉ điều hướng đúng URL bài viết
                return redirect()->route('client.blog.show', ['post' => $slug], 301);
            }
            
            // Nếu là category, forward ngay đến ShopController (không query product)
            if ($slugType['type'] === 'category') {
                $shopController = app(ShopController::class);
                return $shopController->index(request(), $slug);
            }
            
            // Nếu không tìm thấy, check history và category-brand combo
            if ($slugType['type'] === 'not_found') {
                // Check slug history trước
                $history = Cache::remember('slug_history_'.$slug, 86400, function () use ($slug) {
                    return ProductSlugHistory::where('slug', $slug)
                        ->select('product_id')
                        ->first();
                });
                
                if ($history) {
                    $newProduct = Product::active()
                        ->select('slug')
                        ->find($history->product_id);
                    if ($newProduct) {
                        // Invalidate cache và redirect
                        Cache::forget($cacheKey);
                        return redirect()->route('client.product.detail', $newProduct->slug, 301);
                    }
                }
                
                // Nếu slug có dấu gạch ngang, có thể là category-brand combo
                // Forward về ShopController để check
                if (strpos($slug, '-') !== false) {
                    $parts = explode('-', $slug, 2);
                    if (count($parts) === 2) {
                        $shopController = app(ShopController::class);
                        try {
                            return $shopController->categoryBrand($parts[0], $parts[1], request());
                        } catch (\Exception $e) {
                            // Nếu categoryBrand fail, tiếp tục return 404
                        }
                    }
                }
                
                return view('clients.pages.errors.404');
            }
            
            // Nếu là product, load đầy đủ với cache
            // Cache forever với tag để có thể invalidate khi cần (thông qua clearProductDetailCache)
            try {
                $product = Cache::rememberForever('product_detail_'.$slug, function () use ($slug) {
                    // Query với composite index (slug, is_active) - rất nhanh với hàng triệu records
                    $product = Product::where('slug', $slug)
                        ->active()
                        ->with([
                            'variants.primaryVariantImage', 
                            'brand',
                            'primaryCategory.parent' // Eager load parent để breadcrumb không N+1
                        ]) // Eager load để tránh N+1 query
                        ->first();

                    if ($product) {
                        Product::preloadImages([$product]);
                        // Load tags sau khi có product (vì tags là accessor, không phải relationship)
                        $this->preloadTags($product);
                    }

                    return $product;
                });
            } catch (\Throwable $e) {
                // Nếu cache fail, query trực tiếp từ database (fallback)
                Log::warning('ProductController: Cache failed, querying directly', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);
                $product = Product::where('slug', $slug)
                    ->active()
                    ->with([
                        'variants.primaryVariantImage', 
                        'brand',
                        'primaryCategory.parent' // Eager load parent để breadcrumb không N+1
                    ])
                    ->first();
                
                if ($product) {
                    Product::preloadImages([$product]);
                    // Load tags sau khi có product
                    $this->preloadTags($product);
                }
            }

            if ($product) {
                Product::preloadImages([$product]);
                // Load variants nếu chưa có
                if (! $product->relationLoaded('variants')) {
                    $product->load('variants.primaryVariantImage');
                }
            } else {
                // Edge case: Cache nói là product nhưng không tìm thấy (có thể bị deactivate hoặc xóa)
                // Invalidate cache và check lại từ đầu
                Cache::forget($cacheKey);
                Cache::forget('product_detail_'.$slug);
                
                // Check category như fallback (có thể slug này là category)
                $category = Category::where('slug', $slug)
                    ->active()
                    ->select('id', 'slug') // Chỉ select cần thiết
                    ->first();
                if ($category) {
                    // Update cache để lần sau không phải check lại
                    Cache::put($cacheKey, 'category', 3600);
                    $shopController = app(ShopController::class);
                    return $shopController->index(request(), $slug);
                }
                
                // Không tìm thấy cả product và category
                return view('clients.pages.errors.404');
            }

            // Record product view - chuyển sang Queue để không block request
            try {
                RecordProductView::dispatch($product->id)->afterResponse();
            } catch (\Throwable $e) {
                Log::warning('ProductController: Failed to dispatch record view job', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Vouchers (view chi tiết sản phẩm hiện tại không dùng)
            $vouchers = collect();

            // Sản phẩm nổi bật (featured) - Chỉ select cột hiển thị cho sidebar, cache dùng chung toàn hệ thống
            try {
                $featuredPool = Cache::remember('global_featured_sidebar_products', now()->addDay(), function () {
                    $items = Product::active()
                        ->featured()
                        ->select(['id', 'name', 'slug', 'price', 'sale_price', 'image_ids'])
                        ->orderByDesc('id')
                        ->limit(10)
                        ->get() ?? collect();

                    Product::preloadImages($items);

                    return $items;
                });
                $productFeatured = $featuredPool->reject(fn ($p) => $p->id === $product->id)->take(4)->values();
                Product::preloadImages($productFeatured);
            } catch (\Throwable $e) {
                Log::warning('ProductController: Failed to load featured products', ['error' => $e->getMessage()]);
                $productFeatured = collect();
            }

            $cacheKey = 'related_products_' . $product->id;

            try {
                $productRelated = Cache::rememberForever($cacheKey, fn () =>
                    Product::getRelatedProducts($product, 10)
                );
            } catch (\Throwable $e) {
                Log::warning('ProductController: Failed to load related products', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                ]);

                $productRelated = collect();
            }

            // Sản phẩm đi kèm (view single hiện tại không dùng, gán rỗng để tối ưu 100% tài nguyên)
            $includedProducts = collect();

            // Load comments và rating stats - chỉ load 10 đầu tiên
            $comments = collect();
            $totalComments = 0;
            $ratingStats = ['average_rating' => 5.0, 'total_comments' => 0, 'star_1_count' => 0, 'star_2_count' => 0, 'star_3_count' => 0, 'star_4_count' => 0, 'star_5_count' => 0];
            $latestReviews = collect();
            
            try {
                // Cache rating stats trước để dùng luôn total_comments mà không cần query count riêng
                $ratingStatsCacheKey = "product_rating_stats_{$product->id}_{$product->updated_at->timestamp}";
                try {
                    $ratingStats = Cache::remember($ratingStatsCacheKey, now()->addHours(6), function () use ($product) {
                        $commentService = app(\App\Services\CommentService::class);
                        return $commentService->calculateRatingStats('product', $product->id);
                    });
                    $totalComments = (int) ($ratingStats['total_comments'] ?? 0);
                } catch (\Throwable $e) {
                    Log::warning('ProductController: Failed to calculate rating stats', ['error' => $e->getMessage()]);
                }

                // Cache comments với key dựa trên product ID và updated_at
                $commentsCacheKey = "product_comments_{$product->id}_{$product->updated_at->timestamp}";
                $comments = Cache::remember($commentsCacheKey, now()->addHours(6), function () use ($product) {
                    // Lấy comments của người dùng (không phải admin)
                    $comments = Comment::where('commentable_type', 'product')
                        ->where('commentable_id', $product->id)
                        ->whereNull('parent_id')
                        ->approved()
                        ->where(function($q) {
                            $q->whereNull('account_id')
                              ->orWhereHas('account', function($q) {
                                  $q->where('role', '!=', 'admin');
                              });
                        })
                        ->with(['account:id,name,role'])
                        ->select(['id', 'commentable_type', 'commentable_id', 'parent_id', 'account_id', 'name', 'content', 'rating', 'created_at'])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get();

                    // Load admin replies separately
                    $commentIds = $comments->pluck('id');
                    if ($commentIds->isNotEmpty()) {
                        try {
                            $adminReplies = Comment::whereIn('parent_id', $commentIds)
                                ->whereNotNull('account_id')
                                ->whereHas('account', function ($q) {
                                    $q->where('role', 'admin');
                                })
                                ->with(['account:id,name,role'])
                                ->select(['id', 'parent_id', 'account_id', 'content', 'created_at'])
                                ->get()
                                ->keyBy('parent_id');

                            $comments->each(function ($comment) use ($adminReplies) {
                                if ($adminReplies->has($comment->id)) {
                                    $comment->setRelation('adminReply', $adminReplies->get($comment->id));
                                }
                            });
                        } catch (\Throwable $e) {
                            Log::warning('ProductController: Failed to load admin replies', ['error' => $e->getMessage()]);
                        }
                    }

                    return $comments;
                });

                // Load admin reviews (đánh giá từ admin)
                $adminReviewsCacheKey = "product_admin_reviews_{$product->id}_{$product->updated_at->timestamp}";
                $adminReviews = Cache::remember($adminReviewsCacheKey, now()->addHours(6), function () use ($product) {
                    return Comment::where('commentable_type', 'product')
                        ->where('commentable_id', $product->id)
                        ->whereNull('parent_id')
                        ->approved()
                        ->whereNotNull('account_id')
                        ->whereHas('account', function ($q) {
                            $q->where('role', 'admin');
                        })
                        ->with(['account:id,name,role'])
                        ->select(['id', 'commentable_type', 'commentable_id', 'parent_id', 'account_id', 'content', 'rating', 'created_at'])
                        ->orderByDesc('created_at')
                        ->get();
                });
            } catch (\Throwable $e) {
                Log::warning('ProductController: Failed to load comments', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Tính tồn kho còn lại sau khi trừ số lượng trong giỏ hàng hiện tại
            $variantCartQuantities = [];
            $productCartQuantity = 0;

            try {
                $accountId = auth('web')->id();
                $sessionId = request()->session()->getId();

                // Tìm cart ID
                $cartQuery = Cart::query();
                if ($accountId) {
                    $cartQuery->where('account_id', $accountId);
                } else {
                    $cartQuery->whereNull('account_id')->where('session_id', $sessionId);
                }
                $cartId = $cartQuery->latest('id')->value('id');

                if ($cartId) {
                    $items = \App\Models\CartItem::where('cart_id', $cartId)
                        ->where('product_id', $product->id)
                        ->select('product_variant_id', 'quantity')
                        ->get();

                    $productCartQuantity = (int) $items->whereNull('product_variant_id')->sum('quantity');

                    foreach ($items as $item) {
                        if ($item->product_variant_id) {
                            $variantCartQuantities[$item->product_variant_id] = ($variantCartQuantities[$item->product_variant_id] ?? 0) + (int) $item->quantity;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ProductController: Failed to resolve cart quantities for stock display', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Tính số lượng tồn kho sản phẩm chính xác
            if ($product->hasVariants() && $product->variants->isNotEmpty()) {
                $hasUnlimited = $product->variants->contains(fn ($v) => $v->stock_quantity === null);
                if ($hasUnlimited) {
                    $quantityProductDetail = 999999;
                } else {
                    $variantStockTotal = (int) $product->variants->sum('stock_quantity');
                    $quantityProductDetail = $variantStockTotal > 0 ? $variantStockTotal : (int) ($product->stock_quantity ?? 0);
                }
            } else {
                if ($product->stock_quantity === null) {
                    $quantityProductDetail = 999999; // Không giới hạn tồn kho
                } else {
                    $baseStock = (int) $product->stock_quantity;
                    $remaining = max(0, $baseStock - (int) $productCartQuantity);
                    $quantityProductDetail = $remaining;
                }
            }

            // Support staff đã được ViewServiceProvider chia sẻ toàn cục
            $supportStaff = view()->shared('supportStaff') ?? collect();
            $popup = null;
            
            // Tạo gallery images: merge product images + variant images, loại bỏ duplicate
            $galleryImages = collect();
            
            // Thêm ảnh từ product
            if ($product->images && $product->images->isNotEmpty()) {
                foreach ($product->images as $image) {
                    if ($image->url) {
                        $galleryImages->push([
                            'id' => $image->id,
                            'url' => $image->url,
                            'type' => 'product',
                        ]);
                    }
                }
            } elseif ($product->primaryImage && $product->primaryImage->url) {
                $galleryImages->push([
                    'id' => $product->primaryImage->id,
                    'url' => $product->primaryImage->url,
                    'type' => 'product',
                ]);
            }
            
            // Thêm ảnh từ variants (nếu có)
            if ($product->variants && $product->variants->isNotEmpty()) {
                $existingImageIds = $galleryImages->pluck('id')->filter()->toArray();
                $existingUrls = $galleryImages->pluck('url')->filter()->toArray();
                
                foreach ($product->variants as $variant) {
                    if ($variant->primaryVariantImage && $variant->primaryVariantImage->url) {
                        $imageId = $variant->primaryVariantImage->id;
                        $imageUrl = $variant->primaryVariantImage->url;
                        
                        // Chỉ thêm nếu chưa có trong gallery (check cả id và url)
                        if (!in_array($imageId, $existingImageIds) && !in_array($imageUrl, $existingUrls)) {
                            $galleryImages->push([
                                'id' => $imageId,
                                'url' => $imageUrl,
                                'type' => 'variant',
                                'variant_id' => $variant->id,
                                'variant_name' => $variant->name,
                            ]);
                            $existingImageIds[] = $imageId;
                            $existingUrls[] = $imageUrl;
                        }
                    }
                }
            }
            
            return view('clients.pages.single.index',
                compact(
                    'product',
                    'vouchers',
                    'productFeatured',
                    'productRelated',
                    'includedProducts',
                    'quantityProductDetail',
                    'comments',
                    'adminReviews',
                    'ratingStats',
                    'latestReviews',
                    'totalComments',
                    'variantCartQuantities',
                    'productCartQuantity',
                    'supportStaff',
                    'popup',
                    'galleryImages'
                )
            );
        } catch (\Throwable $e) {
            Log::error('ProductController: Fatal error in detail method', [
                'slug' => $slug,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Nếu có lỗi nghiêm trọng, trả về 404 thay vì 500 để tránh ảnh hưởng SEO
            return view('clients.pages.errors.404');
        }
    }

    /**
     * Preload tags để tránh N+1 query trong view
     */
    private function preloadTags(Product $product): void
    {
        $tagIds = $product->tag_ids ?? [];
        if (empty($tagIds)) {
            return;
        }

        $ids = is_array($tagIds) ? $tagIds : json_decode($tagIds, true) ?? [];
        if (empty($ids)) {
            return;
        }

        // Load tags và cache vào product để accessor không query lại
        $tags = Tag::whereIn('id', $ids)
            ->where('is_active', true)
            ->get();

        // Set relation để accessor không query lại
        $product->setRelation('tags', $tags);
    }

    public function wishlist(Request $request)
    {
        $productID = $request->input('product_id');
        $query = Favorite::where('product_id', $productID);

        if (auth('web')->check()) {
            // user đăng nhập
            $query->where('account_id', auth('web')->id());
        } else {
            // user khách dùng session
            $query->where('session_id', session()->getId());
        }

        $wishlist = $query->first();

        if ($wishlist) {
            return redirect()->back()->with('error', 'Sản phẩm đã có trong danh sách yêu thích.');
        }
        $product = Product::where('id', $productID)->active()->first();

        if (! $product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại.');
        }

        try {
            $accountID = auth('web')->user()->id ?? null;
            if ($accountID) {
                Favorite::firstOrCreate([
                    'account_id' => $accountID,
                    'product_id' => $productID,
                    'session_id' => null,
                ]);
            } else {
                Favorite::firstOrCreate([
                    'account_id' => null,
                    'product_id' => $productID,
                    'session_id' => session()->getId(),
                ]);
            }

            return redirect()->back()->with('success', 'Thêm vào danh sách yêu thích thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm vào danh sách yêu thích.');
        }
    }

    public function wishlistRemove(Request $request)
    {
        $productID = $request->input('product_id');

        // Nếu user đăng nhập
        $accountID = auth('web')->user()->id ?? null;

        // Query chung
        $query = Favorite::where('product_id', $productID);

        if ($accountID) {
            $query->where('account_id', $accountID);
        } else {
            $query->where('session_id', session()->getId());
        }

        // Lấy bản ghi
        $favorite = $query->first();

        if (! $favorite) {
            $request->merge(['product_id' => $productID]);

            return $this->wishlist($request);
        }

        try {
            $favorite->delete();

            return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa sản phẩm này.');
        }
    }

    /**
     * Nhận yêu cầu gọi tư vấn từ trang chi tiết sản phẩm.
     */
    public function phoneRequest(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'phone' => ['required', 'regex:/^[0-9]{10,11}$/'],
        ], [
            'product_id.required' => 'Thiếu mã sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ (10-11 chữ số).',
        ]);

        return redirect()->back()->with('success', 'Đã nhận số điện thoại, chúng tôi sẽ liên hệ sớm.');
    }

    /**
     * Xử lý form tư vấn nhanh từ popup thông minh
     */
    public function quickConsultation(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^[0-9]{10,11}$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'message' => ['nullable', 'string', 'max:500'],
            'trigger_type' => ['required', 'string', 'in:view_time,multiple_products,manual'],
            'behavior_data' => ['nullable', 'array'],
        ], [
            'product_id.required' => 'Thiếu mã sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ (10-11 chữ số).',
            'email.email' => 'Email không hợp lệ.',
            'trigger_type.required' => 'Thiếu thông tin trigger.',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $categoryIds = $product->category_ids ?? [];

        \App\Models\QuickConsultationLead::create([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'trigger_type' => $validated['trigger_type'],
            'behavior_data' => array_merge($validated['behavior_data'] ?? [], [
                'category_ids' => $categoryIds,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->input('session_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ gọi lại cho bạn trong thời gian sớm nhất.',
        ]);
    }
}
