<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\ProductViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct(
        private ProductViewService $productViewService
    ) {}

    /**
     * Hiển thị trang profile
     */
    public function index()
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        // Load đầy đủ relationships
        $account->load([
            'addresses',
            'favorites.product',
            'orders' => function ($query) {
                $query->withCount('items')
                    ->latest('created_at')
                    ->limit(10);
            }
        ]);

        // Preload images cho favorites products
        $favoriteProducts = $account->favorites->pluck('product')->filter();
        if ($favoriteProducts->isNotEmpty()) {
            Product::preloadImages($favoriteProducts);
        }

        // Thống kê
        $stats = [
            'total_orders' => Order::where('account_id', $account->id)->count(),
            'pending_orders' => Order::where('account_id', $account->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('account_id', $account->id)->where('status', 'completed')->count(),
            'total_favorites' => $account->favorites()->count(),
            'total_addresses' => $account->addresses()->count(),
        ];

        $recentViews = $this->productViewService->getRecentProducts(10);
        
        // Preload images cho recent views
        if ($recentViews && count($recentViews) > 0) {
            Product::preloadImages($recentViews);
        }

        return view('clients.pages.profile.index', compact('account', 'stats', 'recentViews'));
    }

    /**
     * Cập nhật thông tin profile
     */
    public function update(Request $request)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵýỷỹ\s]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email,'.$account->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên',
            'name.regex' => 'Tên không được chứa số hoặc ký tự đặc biệt',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'phone.regex' => 'Số điện thoại không hợp lệ',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $account->name = $request->name;
            $account->email = $request->email;
            $account->phone = $request->phone ?? $account->phone;
            $account->save();

            return redirect()->route('client.profile.index')
                ->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Profile update error: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (! Hash::check($request->current_password, $account->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $account->password = $request->password; // Model sẽ tự động hash qua setPasswordAttribute
            $account->last_password_changed_at = now();
            $account->save();

            return redirect()->route('client.profile.index')
                ->with('success', 'Đổi mật khẩu thành công!');
        } catch (\Exception $e) {
            Log::error('Change password error: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.']);
        }
    }
}
