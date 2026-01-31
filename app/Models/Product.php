<?php

namespace App\Models;

use App\Models\Concerns\HasImageIds;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    use HasImageIds;

    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'cost_price',
        'stock_quantity',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_canonical',
        'primary_category_id',
        'brand_id',
        'category_included_ids',
        'category_ids',
        'tag_ids',
        'image_ids',
        'link_catalog',
        'video_url',
        'is_featured',
        'locked_by',
        'locked_at',
        'created_by',
        'is_active',
        'category_ids_backup',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'category_ids' => 'array',
        'tag_ids' => 'array',
        'image_ids' => 'array',
        'link_catalog' => 'array',
        'category_ids_backup' => 'array',
        'meta_keywords' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'locked_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'category_included_ids' => 'array',
    ];

    protected array $reviewDisplayCache = [];

    public function slugHistories()
    {
        return $this->hasMany(ProductSlugHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    public function lockedByUser()
    {
        return $this->belongsTo(Account::class, 'locked_by');
    }

    public function primaryCategory()
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Get tags from tag_ids array (not using entity_id)
     */
    public function getTagsAttribute()
    {
        $tagIds = $this->attributes['tag_ids'] ?? null;
        if (empty($tagIds)) {
            return new EloquentCollection;
        }

        $ids = is_array($tagIds) ? $tagIds : json_decode($tagIds, true) ?? [];
        if (empty($ids)) {
            return new EloquentCollection;
        }

        return Tag::whereIn('id', $ids)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Relationship với ProductVariant
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Tất cả variants (kể cả inactive)
     */
    public function allVariants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    /**
     * Kiểm tra sản phẩm có variants không
     */
    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    /**
     * Lấy giá thấp nhất từ variants
     */
    public function getMinVariantPriceAttribute(): ?float
    {
        if (! $this->hasVariants()) {
            return null;
        }

        $variants = $this->variants()->get();
        if ($variants->isEmpty()) {
            return null;
        }

        $prices = $variants->map(function ($variant) {
            return (float) ($variant->sale_price ?? $variant->price);
        });

        return $prices->min();
    }

    /**
     * Lấy giá cao nhất từ variants
     */
    public function getMaxVariantPriceAttribute(): ?float
    {
        if (! $this->hasVariants()) {
            return null;
        }

        $variants = $this->variants()->get();
        if ($variants->isEmpty()) {
            return null;
        }

        $prices = $variants->map(function ($variant) {
            return (float) ($variant->sale_price ?? $variant->price);
        });

        return $prices->max();
    }

    /**
     * Lấy tổng số lượng tồn kho từ tất cả variants
     */
    public function getTotalVariantStockAttribute(): ?int
    {
        if (! $this->hasVariants()) {
            return null;
        }

        $variants = $this->variants()->get();

        // Nếu có variant không giới hạn tồn kho (null) thì trả về null
        if ($variants->contains(fn ($v) => $v->stock_quantity === null)) {
            return null;
        }

        return $variants->sum('stock_quantity');
    }

    public function scopeInCategory($query, array $categoryIds)
    {
        return $query->where(function ($q) use ($categoryIds) {
            $q->whereIn('primary_category_id', $categoryIds)
                ->orWhere(function ($q2) use ($categoryIds) {
                    foreach ($categoryIds as $id) {
                        // Kiểm tra cả integer và string vì JSON có thể lưu dưới cả hai dạng
                        $q2->orWhereJsonContains('category_ids', (int) $id)
                            ->orWhereJsonContains('category_ids', (string) $id);
                    }
                });
        });
    }

    public function getFrameAttribute()
    {
        // Logic để lấy frame, ví dụ:
        if ($this->is_featured) {
            return 'frame-free-ship-hot.png';
        }
        if ($this->sale_price && $this->sale_price < $this->price) {
            return 'frame-price-sale.png';
        }

        return 'frame-free-ship-hot.png';
    }

    public function getLabelAttribute()
    {
        if ($this->is_featured) {
            return 'Nổi bật';
        }
        if ($this->sale_price && $this->sale_price < $this->price) {
            return 'Giảm giá';
        }

        return 'Bán chạy '.date('Y').'';
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopeWithApprovedCommentsMeta($query)
    {
        return $query
            ->withCount(['comments as approved_comments_count' => function ($q) {
                $q->where('is_approved', true)->whereNull('parent_id');
            }])
            ->withAvg(['comments as approved_rating_avg' => function ($q) {
                $q->where('is_approved', true)
                    ->whereNull('parent_id')
                    ->whereNotNull('rating');
            }], 'rating');
    }

    public function getHasRealReviewsAttribute(): bool
    {
        return ($this->approved_comments_count ?? 0) > 0;
    }

    public function getDisplayReviewCountAttribute(): int
    {
        if ($this->has_real_reviews) {
            return (int) $this->approved_comments_count;
        }

        if (! array_key_exists('review_count', $this->reviewDisplayCache)) {
            $this->reviewDisplayCache['review_count'] = rand(10, 1000);
        }

        return $this->reviewDisplayCache['review_count'];
    }

    public function getDisplayRatingValueAttribute(): float
    {
        if ($this->has_real_reviews && ($this->approved_rating_avg ?? null)) {
            $avg = max(1, min(5, (float) $this->approved_rating_avg));

            return round($avg, 1);
        }

        if (! array_key_exists('rating_value', $this->reviewDisplayCache)) {
            $this->reviewDisplayCache['rating_value'] = rand(40, 50) / 10;
        }

        return $this->reviewDisplayCache['rating_value'];
    }

    public function getDisplayRatingStarAttribute(): int
    {
        if (! array_key_exists('rating_star', $this->reviewDisplayCache)) {
            $this->reviewDisplayCache['rating_star'] = (int) round($this->display_rating_value);
        }

        return $this->reviewDisplayCache['rating_star'];
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Scope only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeNew($query)
    {
        $thirtyDaysAgo = now()->subDays(30);

        return $query->active()->where('created_at', '>=', $thirtyDaysAgo);
    }

    /**
     * Scope featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public static function getRelatedProducts(self $product, int $limit = 10)
    {
        $currentId = $product->id;
        $half = intdiv($limit, 2);

        $baseQuery = static::query()
            ->active()
            ->withApprovedCommentsMeta()
            ->where('id', '!=', $currentId)
            ->where(function ($q) use ($product) {
                $q->where('primary_category_id', $product->primary_category_id)
                    ->orWhereJsonContains('category_ids', (int) $product->primary_category_id)
                    ->orWhereJsonContains('category_ids', (string) $product->primary_category_id);
            });

        // 1️⃣ Lấy các sản phẩm trước
        $before = (clone $baseQuery)
            ->where('id', '<', $currentId)
            ->orderByDesc('id')
            ->limit($half)
            ->get();

        // 2️⃣ Lấy các sản phẩm sau
        $after = (clone $baseQuery)
            ->where('id', '>', $currentId)
            ->orderBy('id')
            ->limit($half)
            ->get();

        // 3️⃣ Fallback nếu thiếu ở phía trước: lấy thêm ở phía sau
        if ($before->count() < $half) {
            $need = $half - $before->count();

            $extraAfter = (clone $baseQuery)
                ->where('id', '>', optional($after->last())->id ?? $currentId)
                ->orderBy('id')
                ->limit($need)
                ->get();

            $after = $after->merge($extraAfter);
        }

        // 4️⃣ Fallback nếu thiếu ở phía sau: lấy thêm ở phía trước
        if ($after->count() < $half) {
            $need = $half - $after->count();

            $extraBefore = (clone $baseQuery)
                ->where('id', '<', optional($before->first())->id ?? $currentId)
                ->orderByDesc('id')
                ->limit($need)
                ->get();

            $before = $extraBefore->merge($before);
        }

        // 5️⃣ Sắp xếp: trước (id tăng dần) rồi tới sau
        $collection = $before
            ->reverse()
            ->merge($after)
            ->take($limit)
            ->values();

        static::preloadImages($collection);

        return $collection;
    }

    public function extraCategories()
    {
        return Category::whereIn('id', $this->category_ids ?? [])->get();
    }

    public function resolveCartPrice(): float
    {
        if ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (float) $this->sale_price;
        }

        return (float) $this->price;
    }

    public function newCollection(array $models = []): EloquentCollection
    {
        $collection = new EloquentCollection($models);
        static::preloadImages($collection);

        return $collection;
    }

    protected static function booted(): void
    {
        static::saved(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });

        static::deleted(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });

        static::updating(function (self $product) {
            if ($product->isDirty('slug')) {
                $oldSlug = $product->getOriginal('slug');
                if ($oldSlug) {
                    ProductSlugHistory::firstOrCreate(
                        ['slug' => $oldSlug],
                        ['product_id' => $product->id]
                    );
                    // Invalidate cache cho slug cũ
                    Cache::forget('product_detail_'.$oldSlug);
                    Cache::forget('slug_type_'.$oldSlug);
                }
            }
            // Invalidate cache khi is_active thay đổi
            if ($product->isDirty('is_active')) {
                $slug = $product->slug ?? $product->getOriginal('slug');
                if ($slug) {
                    Cache::forget('product_detail_'.$slug);
                    Cache::forget('slug_type_'.$slug);
                }
            }
        });
        
        static::deleting(function (self $product) {
            // Invalidate cache khi xóa product
            $slug = $product->slug;
            if ($slug) {
                Cache::forget('product_detail_'.$slug);
                Cache::forget('slug_type_'.$slug);
            }
        });
    }
}

class ProductSlugHistory extends Model
{
    protected $table = 'product_slug_histories';

    protected $fillable = [
        'product_id',
        'slug',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
