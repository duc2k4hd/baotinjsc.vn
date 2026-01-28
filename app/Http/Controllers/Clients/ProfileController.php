<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Services\ProductViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Lưu địa chỉ mới
     */
    public function storeAddress(Request $request)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'detail_address' => ['required', 'string', 'max:500'],
            'ward' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'phone_number.regex' => 'Số điện thoại không hợp lệ',
            'detail_address.required' => 'Vui lòng nhập địa chỉ chi tiết',
            'ward.required' => 'Vui lòng nhập phường/xã',
            'district.required' => 'Vui lòng nhập quận/huyện',
            'province.required' => 'Vui lòng nhập tỉnh/thành phố',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('tab', 'addresses');
        }

        try {
            DB::beginTransaction();

            // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
            if ($request->is_default) {
                Address::where('account_id', $account->id)
                    ->update(['is_default' => false]);
            }

            // Nếu đây là địa chỉ đầu tiên, tự động đặt làm mặc định
            $isFirstAddress = Address::where('account_id', $account->id)->count() === 0;

            $address = Address::create([
                'account_id' => $account->id,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'detail_address' => $request->detail_address,
                'ward' => $request->ward,
                'district' => $request->district,
                'province' => $request->province,
                'province_code' => $request->province_code ?? null,
                'district_code' => $request->district_code ?? null,
                'ward_code' => $request->ward_code ?? null,
                'postal_code' => $request->postal_code ?? null,
                'country' => $request->country ?? 'Việt Nam',
                'address_type' => $request->address_type ?? 'home',
                'notes' => $request->notes ?? null,
                'is_default' => $isFirstAddress || ($request->is_default ?? false),
            ]);

            DB::commit();

            return redirect()->route('client.profile.index', ['#addresses'])
                ->with('success', 'Thêm địa chỉ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store address error: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'])
                ->with('tab', 'addresses');
        }
    }

    /**
     * Cập nhật địa chỉ
     */
    public function updateAddress(Request $request, $id)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $address = Address::where('id', $id)
            ->where('account_id', $account->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'detail_address' => ['required', 'string', 'max:500'],
            'ward' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'phone_number.regex' => 'Số điện thoại không hợp lệ',
            'detail_address.required' => 'Vui lòng nhập địa chỉ chi tiết',
            'ward.required' => 'Vui lòng nhập phường/xã',
            'district.required' => 'Vui lòng nhập quận/huyện',
            'province.required' => 'Vui lòng nhập tỉnh/thành phố',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('tab', 'addresses');
        }

        try {
            DB::beginTransaction();

            // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
            if ($request->is_default) {
                Address::where('account_id', $account->id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update([
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'detail_address' => $request->detail_address,
                'ward' => $request->ward,
                'district' => $request->district,
                'province' => $request->province,
                'province_code' => $request->province_code ?? $address->province_code,
                'district_code' => $request->district_code ?? $address->district_code,
                'ward_code' => $request->ward_code ?? $address->ward_code,
                'postal_code' => $request->postal_code ?? $address->postal_code,
                'address_type' => $request->address_type ?? $address->address_type,
                'notes' => $request->notes ?? $address->notes,
                'is_default' => $request->is_default ?? $address->is_default,
            ]);

            DB::commit();

            return redirect()->route('client.profile.index', ['#addresses'])
                ->with('success', 'Cập nhật địa chỉ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update address error: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'])
                ->with('tab', 'addresses');
        }
    }

    /**
     * Xóa địa chỉ
     */
    public function deleteAddress($id)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $address = Address::where('id', $id)
            ->where('account_id', $account->id)
            ->firstOrFail();

        try {
            $address->delete();

            return redirect()->route('client.profile.index', ['#addresses'])
                ->with('success', 'Xóa địa chỉ thành công!');
        } catch (\Exception $e) {
            Log::error('Delete address error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'])
                ->with('tab', 'addresses');
        }
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefaultAddress($id)
    {
        $account = Auth::guard('web')->user();

        if (! $account) {
            return redirect()->route('client.auth.login');
        }

        $address = Address::where('id', $id)
            ->where('account_id', $account->id)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Bỏ mặc định của tất cả địa chỉ khác
            Address::where('account_id', $account->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);

            // Đặt địa chỉ này làm mặc định
            $address->update(['is_default' => true]);

            DB::commit();

            return redirect()->route('client.profile.index', ['#addresses'])
                ->with('success', 'Đặt địa chỉ mặc định thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Set default address error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'])
                ->with('tab', 'addresses');
        }
    }
}
