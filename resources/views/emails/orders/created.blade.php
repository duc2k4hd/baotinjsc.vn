<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng #{{ $order->code }} đã được tạo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .order-info {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .order-info-item {
            margin: 8px 0;
        }
        .order-info-label {
            font-weight: 600;
            color: #333;
            display: inline-block;
            width: 140px;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .order-items th,
        .order-items td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-items th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .total-box {
            background: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .total-box .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 16px;
        }
        .total-box .final-total {
            font-size: 20px;
            font-weight: 700;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
            margin-top: 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: 600;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Đơn hàng #{{ $order->code }} đã được tạo thành công!</h1>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $order->receiver_name ?? $order->account?->name }}</strong>,</p>

            <p>Cảm ơn bạn đã đặt hàng tại <strong>{{ $siteName }}</strong>! Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>

            <div class="order-info">
                <div class="order-info-item">
                    <span class="order-info-label">Mã đơn hàng:</span>
                    <strong>#{{ $order->code }}</strong>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Ngày đặt hàng:</span>
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Trạng thái:</span>
                    <span style="color: #28a745; font-weight: 600;">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Phương thức thanh toán:</span>
                    {{ $order->payment_method === 'cod' ? 'COD (Thanh toán khi nhận hàng)' : ucfirst($order->payment_method) }}
                </div>
            </div>

            <h3 style="margin-top: 30px;">📦 Chi tiết đơn hàng</h3>
            <div class="order-items">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                @if($item->variant)
                                    <br><small style="color: #666;">{{ $item->variant->name ?? '' }}</small>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td><strong>{{ number_format($item->total, 0, ',', '.') }} đ</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-box">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                </div>
                @if($order->shipping_fee > 0)
                <div class="total-row">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                </div>
                @endif
                @if($order->voucher_discount > 0)
                <div class="total-row" style="color: #28a745;">
                    <span>Giảm giá ({{ $order->voucher_code }}):</span>
                    <span>-{{ number_format($order->voucher_discount, 0, ',', '.') }} đ</span>
                </div>
                @endif
                <div class="total-row final-total">
                    <span>Tổng cộng:</span>
                    <span>{{ number_format($order->final_price, 0, ',', '.') }} đ</span>
                </div>
            </div>

            <h3 style="margin-top: 30px;">📍 Địa chỉ giao hàng</h3>
            <div class="order-info">
                <p style="margin: 5px 0;"><strong>{{ $order->receiver_name }}</strong></p>
                <p style="margin: 5px 0;">📞 {{ $order->receiver_phone }}</p>
                @if($order->receiver_email)
                <p style="margin: 5px 0;">📧 {{ $order->receiver_email }}</p>
                @endif
                <p style="margin: 5px 0;">📍 {{ $order->shipping_address }}</p>
            </div>

            @if($order->customer_note)
            <div class="order-info" style="background: #fff3cd; border-left-color: #ffc107;">
                <strong>📝 Ghi chú:</strong>
                <p style="margin: 5px 0;">{{ $order->customer_note }}</p>
            </div>
            @endif

            <div class="button-container">
                <a href="{{ $siteUrl }}/tai-khoan/don-hang/{{ $order->code }}" class="button">Xem chi tiết đơn hàng</a>
            </div>

            <p style="margin-top: 30px;">
                <strong>Lưu ý:</strong> 
                @if($order->payment_method === 'cod')
                    Bạn sẽ thanh toán khi nhận hàng. Vui lòng chuẩn bị đúng số tiền để thanh toán.
                @else
                    Vui lòng hoàn tất thanh toán để đơn hàng được xử lý nhanh chóng.
                @endif
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $siteName }}</strong></p>
            <p>
                <a href="{{ $siteUrl }}">{{ $siteUrl }}</a>
            </p>
            <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
            <p>Nếu bạn có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ.</p>
            <p style="margin-top: 20px; color: #999;">
                &copy; {{ date('Y') }} {{ $siteName }}. Tất cả quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>

