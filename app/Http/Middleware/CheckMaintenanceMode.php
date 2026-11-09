<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Xử lý kiểm tra chế độ bảo trì hệ thống.
     * Chặn ngay lập tức ở đầu pipeline request đối với người dùng thông thường,
     * chỉ cho phép Admin và các route quản trị truy cập.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra trạng thái bảo trì từ Cache (0ms latency, không query database mỗi request)
        $isMaintenance = (bool) Cache::rememberForever('maintenance_mode_status', function () {
            $setting = Setting::where('key', 'maintenance_mode')->first();
            return $setting ? (bool) $setting->getParsedValue() : false;
        });

        // Nếu website KHÔNG bảo trì, cho qua ngay lập tức
        if (! $isMaintenance) {
            return $next($request);
        }

        // 2. NGOẠI LỆ ĐƯỢC PHÉP TRUY CẬP KHI ĐANG BẢO TRÌ:
        // A. Tuyến đường trang quản trị Admin và đăng nhập Admin
        if (
            $request->is('admin*') ||
            $request->is('admin') ||
            $request->routeIs('admin.*') ||
            $request->is('api/admin*')
        ) {
            return $next($request);
        }

        // B. Người dùng đã đăng nhập với vai trò Admin hoặc Writer
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user && in_array($user->role, [Account::ROLE_ADMIN, Account::ROLE_WRITER], true)) {
                return $next($request);
            }
        }

        // C. Hỗ trợ xem trước trang bảo trì qua secret key nếu cần (ví dụ cho kiểm thử)
        if ($request->query('preview_maintenance') === '1' && Auth::guard('web')->check()) {
            return $this->renderMaintenanceResponse($request);
        }

        // 3. CHẶN TOÀN BỘ CÁC TRANG CLIENT KHÁC - Trả về 503 Service Unavailable
        return $this->renderMaintenanceResponse($request);
    }

    /**
     * Render phản hồi thông báo bảo trì phù hợp (JSON hoặc View 503 độc lập)
     */
    protected function renderMaintenanceResponse(Request $request): Response
    {
        // Nếu là yêu cầu API hoặc AJAX JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'maintenance',
                'message' => 'Hệ thống Bảo Tín JSC hiện đang được bảo trì định kỳ để nâng cấp. Vui lòng thử lại sau ít phút.',
                'retry_after' => 1800,
            ], 503, [
                'Retry-After' => '1800',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        // Lấy settings từ cache (đã được cache vĩnh viễn ở ViewServiceProvider)
        $settings = Cache::rememberForever('settings', function () {
            return Setting::active()
                ->get()
                ->mapWithKeys(fn ($s) => [$s->key => $s->getParsedValue()])
                ->toArray();
        });

        return response()->view('clients.pages.errors.503', [
            'settings' => (object) $settings,
        ], 503, [
            'Retry-After' => '1800',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}

