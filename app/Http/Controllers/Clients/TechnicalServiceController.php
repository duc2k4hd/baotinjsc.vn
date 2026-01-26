<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class TechnicalServiceController extends Controller
{
    public function index(): View
    {
        // Lấy một số sản phẩm nổi bật để hiển thị
        $featuredProducts = Product::active()
            ->orderByDesc('created_at')
            ->inRandomOrder()
            ->limit(6)
            ->get() ?? collect();

        Product::preloadImages($featuredProducts);

        return view('clients.pages.technical-service.index', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
