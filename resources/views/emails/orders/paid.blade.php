<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công - Đơn hàng #{{ $order->code }}</title>
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
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .success-box {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .success-box h2 {
            color: #28a745;
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .order-info {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
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
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
            <h1>💳 Thanh toán thành công!</h1>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $order->receiver_name ?? $order->account?->name }}</strong>,</p>

            <div class="success-box">
                <h2>✅ Thanh toán đã được xác nhận</h2>
                <p style="margin: 0; font-size: 16px;">Đơn hàng của bạn đang được xử lý và sẽ được giao hàng sớm nhất có thể.</p>
            </div>

            <div class="order-info">
                <div class="order-info-item">
                    <span class="order-info-label">Mã đơn hàng:</span>
                    <strong>#{{ $order->code }}</strong>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Số tiền đã thanh toán:</span>
                    <strong style="color: #28a745; font-size: 18px;">{{ number_format($order->final_price, 0, ',', '.') }} đ</strong>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Phương thức thanh toán:</span>
                    {{ ucfirst($order->payment_method) }}
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Trạng thái đơn hàng:</span>
                    <span style="color: #007bff; font-weight: 600;">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Thời gian thanh toán:</span>
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="total-box">
                <div class="total-row final-total">
                    <span>Tổng đã thanh toán:</span>
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

            <div class="button-container">
                <a href="{{ $siteUrl }}/don-hang/{{ $order->id }}" class="button">Xem chi tiết đơn hàng</a>
            </div>

            <p style="margin-top: 30px;">
                <strong>Lưu ý:</strong> 
                Đơn hàng của bạn đã được xác nhận thanh toán và đang được chuẩn bị để giao hàng. 
                Chúng tôi sẽ thông báo cho bạn ngay khi đơn hàng được gửi đi.
            </p>

            <p style="background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #ffc107;">
                <strong>💡 Mẹo:</strong> Bạn có thể theo dõi trạng thái đơn hàng bất cứ lúc nào trên website của chúng tôi.
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

