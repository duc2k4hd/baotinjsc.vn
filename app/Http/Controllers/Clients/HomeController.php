<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the Homepage
     */
    public function index()
    {
        // 1. Banners: Gom nhóm theo vị trí (1: Hero Slider, 2: Sidebar Top, 3: Sidebar Bottom, 4-6: Strip)
        $banners = Banner::active()
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('position');

        // 2. Categories cho Hero Menu và Shop By Categories
        $mainCategories = Category::active()
            ->whereNull('parent_id')
            ->with(['children.children'])
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->take(9)
            ->get();

        $allCategories = Category::active()
            ->whereNull('parent_id')
            ->with(['children.children'])
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // 3. Flash Sale: 5 sản phẩm mỗi ngày
        $secondsUntilMidnight = max(60, now()->diffInSeconds(now()->endOfDay()));
        $flashSaleProductIds = Cache::remember('home_flash_sale_product_ids', $secondsUntilMidnight, function () {
            return Product::active()->inRandomOrder()->take(5)->pluck('id')->toArray();
        });

        // 4. Featured Products: 17 sản phẩm nổi bật
        $featuredProductIds = Cache::remember('home_featured_product_ids', 1800, function () {
            $ids = Product::active()
                ->featured()
                ->latest('id')
                ->take(17)
                ->pluck('id')
                ->toArray();

            if (count($ids) < 10) {
                $extra = Product::active()->latest('id')->take(17)->pluck('id')->toArray();
                $ids = array_values(array_unique(array_merge($ids, $extra)));
            }
            return $ids;
        });

        // 5. Category Sections: 5 danh mục nổi bật kèm sản phẩm
        $categorySectionsData = Cache::remember('home_category_sections_map', 1800, function () {
            $allCats = Category::active()
                ->select(['id', 'name', 'slug', 'parent_id', 'image', 'order'])
                ->orderBy('order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            if ($allCats->isEmpty()) {
                return [];
            }

            $childrenMap = [];
            foreach ($allCats as $c) {
                $pId = $c->parent_id ?: 0;
                $childrenMap[$pId][] = $c->id;
            }

            $getDescendantIds = function ($parentId) use (&$getDescendantIds, &$childrenMap) {
                $ids = [$parentId];
                if (isset($childrenMap[$parentId])) {
                    foreach ($childrenMap[$parentId] as $childId) {
                        $ids = array_merge($ids, $getDescendantIds($childId));
                    }
                }
                return $ids;
            };

            $productCounts = Product::active()
                ->selectRaw('primary_category_id, COUNT(*) as aggregate')
                ->groupBy('primary_category_id')
                ->pluck('aggregate', 'primary_category_id')
                ->toArray();

            $candidates = [];
            foreach ($allCats as $cat) {
                $allCatIds = $getDescendantIds($cat->id);
                $pCount = 0;
                foreach ($allCatIds as $cid) {
                    $pCount += $productCounts[$cid] ?? 0;
                }

                if ($pCount > 0) {
                    $candidates[] = [
                        'category' => $cat,
                        'category_id' => $cat->id,
                        'cat_ids' => $allCatIds,
                        'count' => $pCount,
                        'is_root' => is_null($cat->parent_id),
                    ];
                }
            }

            usort($candidates, function ($a, $b) {
                if ($a['is_root'] === $b['is_root']) {
                    return $b['count'] <=> $a['count'];
                }
                return $a['is_root'] ? -1 : 1;
            });

            $selected = array_slice($candidates, 0, 5);
            if (empty($selected)) {
                return [];
            }

            $allTargetCatIds = [];
            foreach ($selected as $s) {
                $allTargetCatIds = array_merge($allTargetCatIds, $s['cat_ids']);
            }
            $allTargetCatIds = array_values(array_unique($allTargetCatIds));

            $rawProducts = Product::active()
                ->select(['id', 'primary_category_id', 'created_at'])
                ->whereIn('primary_category_id', $allTargetCatIds)
                ->latest('id')
                ->get();

            $result = [];
            foreach ($selected as $item) {
                $targetIdsLookup = array_flip($item['cat_ids']);
                $pIds = $rawProducts->filter(function ($p) use ($targetIdsLookup) {
                    return isset($targetIdsLookup[$p->primary_category_id]);
                })->take(10)->pluck('id')->toArray();

                if (!empty($pIds)) {
                    $catModel = $item['category'];
                    $result[] = [
                        'category_id' => $catModel->id,
                        'category' => [
                            'id' => $catModel->id,
                            'name' => $catModel->name,
                            'slug' => $catModel->slug,
                            'image' => $catModel->image,
                        ],
                        'product_ids' => $pIds
                    ];
                }
            }

            return $result;
        });

        // 6. Gom toàn bộ Product IDs cần hiển thị để query 1 lần (Batch Fetching)
        $allSectionProductIds = [];
        if (!empty($categorySectionsData)) {
            foreach ($categorySectionsData as $sec) {
                if (!empty($sec['product_ids'])) {
                    $allSectionProductIds = array_merge($allSectionProductIds, $sec['product_ids']);
                }
            }
        }

        $allNeededProductIds = array_values(array_unique(array_merge(
            $flashSaleProductIds,
            $featuredProductIds,
            $allSectionProductIds
        )));

        $allProductsKeyed = !empty($allNeededProductIds)
            ? Product::active()
                ->whereIn('id', $allNeededProductIds)
                ->get()
                ->keyBy('id')
            : collect();

        // Nạp trước hình ảnh vào cache pool của HasImageIds
        Product::preloadImages($allProductsKeyed);

        // Gán Flash Sale
        $flashSaleProducts = collect();
        foreach ($flashSaleProductIds as $id) {
            if (isset($allProductsKeyed[$id])) {
                $flashSaleProducts->push($allProductsKeyed[$id]);
            }
        }

        // Gán Featured Products
        $featuredProducts = collect();
        foreach ($featuredProductIds as $id) {
            if (isset($allProductsKeyed[$id])) {
                $featuredProducts->push($allProductsKeyed[$id]);
            }
        }

        // Gán Category Sections
        $categorySections = collect();
        if (!empty($categorySectionsData)) {
            foreach ($categorySectionsData as $sec) {
                $secProducts = collect();
                $pIds = $sec['product_ids'] ?? [];
                foreach ($pIds as $pid) {
                    if (isset($allProductsKeyed[$pid])) {
                        $secProducts->push($allProductsKeyed[$pid]);
                    }
                }
                if ($secProducts->isNotEmpty()) {
                    $cData = is_array($sec['category']) ? $sec['category'] : (array)$sec['category'];
                    $categorySections->push((object)[
                        'category' => (object)$cData,
                        'products' => $secProducts
                    ]);
                }
            }
        }

        // 7. Blog Sections: Gom nhóm bài viết cẩm nang theo chuyên mục
        $blogSectionsData = Cache::remember('home_blog_sections_map', 3600, function () {
            $catIdsWithPosts = Post::where('status', 'published')
                ->pluck('category_id')
                ->unique()
                ->filter()
                ->take(4)
                ->toArray();

            $postCats = Category::active()
                ->whereIn('id', $catIdsWithPosts)
                ->select(['id', 'name', 'slug'])
                ->get();

            $result = [];
            foreach ($postCats as $cat) {
                $postIds = Post::where('category_id', $cat->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->take(3)
                    ->pluck('id')
                    ->toArray();

                if (!empty($postIds)) {
                    $result[] = [
                        'category' => [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug
                        ],
                        'post_ids' => $postIds
                    ];
                }
            }
            return $result;
        });

        $blogSections = collect();
        if (!empty($blogSectionsData)) {
            $allPostIds = [];
            foreach ($blogSectionsData as $bSec) {
                if (!empty($bSec['post_ids'])) {
                    $allPostIds = array_merge($allPostIds, $bSec['post_ids']);
                }
            }
            $allPostIds = array_values(array_unique($allPostIds));

            $allPosts = !empty($allPostIds)
                ? Post::whereIn('id', $allPostIds)
                    ->select(['id', 'title', 'slug', 'excerpt', 'published_at', 'category_id', 'created_at', 'thumbnail', 'image_ids'])
                    ->get()
                    ->keyBy('id')
                : collect();

            Post::preloadImages($allPosts);

            foreach ($blogSectionsData as $bSec) {
                $catPosts = collect();
                $pIds = $bSec['post_ids'] ?? [];
                foreach ($pIds as $pid) {
                    if (isset($allPosts[$pid])) {
                        $catPosts->push($allPosts[$pid]);
                    }
                }
                if ($catPosts->isNotEmpty()) {
                    $catData = is_array($bSec['category']) ? $bSec['category'] : (array)$bSec['category'];
                    $blogSections->push((object)[
                        'id' => $catData['id'] ?? 0,
                        'name' => $catData['name'] ?? '',
                        'slug' => $catData['slug'] ?? '',
                        'posts' => $catPosts
                    ]);
                }
            }
        }

        return view('clients.pages.home.index', compact(
            'banners',
            'mainCategories',
            'allCategories',
            'flashSaleProducts',
            'featuredProducts',
            'categorySections',
            'blogSections'
        ));
    }
}
