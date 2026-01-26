@extends('admins.layouts.master')

@section('title', 'Tạo đơn hàng từ giỏ hàng')
@section('page-title', '📦 Tạo đơn hàng')

@push('head')
    <link rel="shortcut icon" href="{{ asset('admins/img/icons/cart-icon.webp') }}" type="image/x-icon">
@endpush

@push('styles')
    <style>
        .card {
            background:#fff;
            border-radius:10px;
            padding:16px;
            box-shadow:0 1px 6px rgba(15,23,42,0.06);
            margin-bottom:16px;
        }
        .card > h3 {
            margin:0 0 12px;
            font-size:16px;
            font-weight:600;
            color:#0f172a;
        }
        .grid-2 {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:12px 16px;
        }
        .grid-3 {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:12px 16px;
        }
        .form-control, textarea, select {
            width:100%;
            padding:8px 10px;
            border:1px solid #cbd5f5;
            border-radius:6px;
            font-size:13px;
        }
        label {
            display:block;
            font-size:13px;
            font-weight:500;
            margin-bottom:4px;
            color:#111827;
        }
        .readonly-field {
            background:#f8fafc;
            border:1px dashed #cbd5f5;
            padding:8px 10px;
            border-radius:6px;
            font-size:13px;
        }
        .summary-item {
            display:flex;
            justify-content:space-between;
            padding:8px 0;
            border-bottom:1px solid #eef2f7;
        }
        .summary-item.total {
            font-weight:600;
            font-size:16px;
            border-top:2px solid #eef2f7;
            margin-top:8px;
            padding-top:12px;
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('admin.carts.store-order', $cart) }}" method="POST">
        @csrf

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="margin:0;">Tạo đơn hàng từ giỏ hàng</h2>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.carts.show', $cart) }}" class="btn btn-secondary">↩️ Quay lại</a>
                <button type="submit" class="btn btn-primary">💾 Tạo đơn hàng</button>
            </div>
        </div>

        <div class="card">
            <h3>Thông tin giỏ hàng</h3>
            <div class="grid-3">
                <div>
                    <label>Mã giỏ hàng</label>
                    <div class="readonly-field">{{ $cart->code ?? '—' }}</div>
                </div>
                <div>
                    <label>Người dùng</label>
                    <div class="readonly-field">
                        @if($cart->account)
                            {{ $cart->account->name ?? $cart->account->email }}
                        @else
                            Khách ({{ substr($cart->session_id, 0, 16) }}...)
                        @endif
                    </div>
                </div>
                <div>
                    <label>Tổng số lượng</label>
                    <div class="readonly-field">{{ number_format($cart->total_quantity) }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Thông tin người nhận</h3>
            <div class="grid-2">
                <div>
                    <label>Họ tên <span style="color:red;">*</span></label>
                    <input type="text" name="receiver_name" class="form-control" 
                           value="{{ old('receiver_name', $cart->account?->name) }}" required>
                </div>
                <div>
                    <label>Số điện thoại <span style="color:red;">*</span></label>
                    <input type="text" name="receiver_phone" class="form-control" 
                           value="{{ old('receiver_phone') }}" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="receiver_email" class="form-control" 
                           value="{{ old('receiver_email', $cart->account?->email) }}">
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Địa chỉ giao hàng</h3>
            <div class="grid-2">
                <div>
                    <label>Địa chỉ chi tiết <span style="color:red;">*</span></label>
                    <textarea name="shipping_address" rows="3" class="form-control" required>{{ old('shipping_address') }}</textarea>
                </div>
                <div>
                    <label>Tỉnh/Thành phố ID <span style="color:red;">*</span></label>
                    <input type="number" name="shipping_province_id" class="form-control" 
                           value="{{ old('shipping_province_id') }}" required>
                </div>
                <div>
                    <label>Quận/Huyện ID <span style="color:red;">*</span></label>
                    <input type="number" name="shipping_district_id" class="form-control" 
                           value="{{ old('shipping_district_id') }}" required>
                </div>
                <div>
                    <label>Phường/Xã ID <span style="color:red;">*</span></label>
                    <input type="number" name="shipping_ward_id" class="form-control" 
                           value="{{ old('shipping_ward_id') }}" required>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Thanh toán & Vận chuyển</h3>
            <div class="grid-3">
                <div>
                    <label>Phương thức thanh toán <span style="color:red;">*</span></label>
                    <select name="payment_method" class="form-control" required>
                        <option value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'selected' : '' }}>COD (Thanh toán khi nhận hàng)</option>
                        <option value="qr" {{ old('payment_method') === 'qr' ? 'selected' : '' }}>QR Code</option>
                        <option value="momo" {{ old('payment_method') === 'momo' ? 'selected' : '' }}>MoMo</option>
                        <option value="zalopay" {{ old('payment_method') === 'zalopay' ? 'selected' : '' }}>ZaloPay</option>
                    </select>
                </div>
                <div>
                    <label>Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-control">
                        <option value="pending" {{ old('payment_status', 'pending') === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ old('payment_status') === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                    </select>
                </div>
                <div>
                    <label>Đơn vị vận chuyển</label>
                    <select name="shipping_partner" class="form-control">
                        <option value="viettelpost" {{ old('shipping_partner', 'viettelpost') === 'viettelpost' ? 'selected' : '' }}>ViettelPost</option>
                        <option value="ghtk" {{ old('shipping_partner') === 'ghtk' ? 'selected' : '' }}>GHTK</option>
                    </select>
                </div>
                <div>
                    <label>Trạng thái đơn hàng</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ old('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Chi phí & Khuyến mãi</h3>
            <div class="grid-3">
                <div>
                    <label>Phí vận chuyển</label>
                    <input type="number" name="shipping_fee" class="form-control" 
                           value="{{ old('shipping_fee', 0) }}" min="0" step="1000">
                </div>
                <div>
                    <label>Thuế</label>
                    <input type="number" name="tax" class="form-control" 
                           value="{{ old('tax', 0) }}" min="0" step="1000">
                </div>
                <div>
                    <label>Giảm giá</label>
                    <input type="number" name="discount" class="form-control" 
                           value="{{ old('discount', 0) }}" min="0" step="1000">
                </div>
                <div>
                    <label>Mã voucher</label>
                    <input type="text" name="voucher_code" class="form-control" 
                           value="{{ old('voucher_code') }}">
                </div>
                <div>
                    <label>Giảm giá từ voucher</label>
                    <input type="number" name="voucher_discount" class="form-control" 
                           value="{{ old('voucher_discount', 0) }}" min="0" step="1000">
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Tóm tắt đơn hàng</h3>
            <div>
                <div class="summary-item">
                    <span>Tổng tiền sản phẩm:</span>
                    <strong>{{ number_format($cart->total_price) }} đ</strong>
                </div>
                <div class="summary-item">
                    <span>Phí vận chuyển:</span>
                    <span id="shipping-fee-display">0 đ</span>
                </div>
                <div class="summary-item">
                    <span>Thuế:</span>
                    <span id="tax-display">0 đ</span>
                </div>
                <div class="summary-item">
                    <span>Giảm giá:</span>
                    <span id="discount-display">0 đ</span>
                </div>
                <div class="summary-item">
                    <span>Giảm giá voucher:</span>
                    <span id="voucher-discount-display">0 đ</span>
                </div>
                <div class="summary-item total">
                    <span>Thành tiền:</span>
                    <strong id="final-price-display">{{ number_format($cart->total_price) }} đ</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Ghi chú</h3>
            <div>
                <label>Ghi chú khách hàng</label>
                <textarea name="customer_note" rows="3" class="form-control">{{ old('customer_note') }}</textarea>
            </div>
            <div style="margin-top:12px;">
                <label>Ghi chú nội bộ</label>
                <textarea name="admin_note" rows="3" class="form-control">{{ old('admin_note') }}</textarea>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-bottom:16px;">
            <a href="{{ route('admin.carts.show', $cart) }}" class="btn btn-secondary">↩️ Quay lại</a>
            <button type="submit" class="btn btn-primary">💾 Tạo đơn hàng</button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shippingFeeInput = document.querySelector('input[name="shipping_fee"]');
            const taxInput = document.querySelector('input[name="tax"]');
            const discountInput = document.querySelector('input[name="discount"]');
            const voucherDiscountInput = document.querySelector('input[name="voucher_discount"]');
            
            const shippingFeeDisplay = document.getElementById('shipping-fee-display');
            const taxDisplay = document.getElementById('tax-display');
            const discountDisplay = document.getElementById('discount-display');
            const voucherDiscountDisplay = document.getElementById('voucher-discount-display');
            const finalPriceDisplay = document.getElementById('final-price-display');

            const basePrice = {{ $cart->total_price }};

            function updateFinalPrice() {
                const shippingFee = parseFloat(shippingFeeInput.value) || 0;
                const tax = parseFloat(taxInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                const voucherDiscount = parseFloat(voucherDiscountInput.value) || 0;

                const finalPrice = basePrice + shippingFee + tax - discount - voucherDiscount;

                shippingFeeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(shippingFee) + ' đ';
                taxDisplay.textContent = new Intl.NumberFormat('vi-VN').format(tax) + ' đ';
                discountDisplay.textContent = new Intl.NumberFormat('vi-VN').format(discount) + ' đ';
                voucherDiscountDisplay.textContent = new Intl.NumberFormat('vi-VN').format(voucherDiscount) + ' đ';
                finalPriceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(Math.max(0, finalPrice)) + ' đ';
            }

            [shippingFeeInput, taxInput, discountInput, voucherDiscountInput].forEach(input => {
                input.addEventListener('input', updateFinalPrice);
            });

            updateFinalPrice();
        });
    </script>
    @endpush
@endsection

