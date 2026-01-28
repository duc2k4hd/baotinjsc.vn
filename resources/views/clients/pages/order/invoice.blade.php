<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn {{ $order->code }} - {{ $settings->site_name ?? 'Bảo Tín JSC' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .invoice-meta {
            font-size: 13px;
            color: #666;
        }

        .company-info {
            text-align: right;
            font-size: 13px;
            line-height: 1.8;
        }

        .company-info strong {
            font-size: 18px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .invoice-body {
            margin-top: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-box {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .info-box h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .info-item {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-item strong {
            color: #333;
            display: inline-block;
            min-width: 100px;
        }

        .info-item span {
            color: #666;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .products-table thead {
            background: #667eea;
            color: #fff;
        }

        .products-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .products-table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
        }

        .summary-row.discount {
            color: #28a745;
        }

        .print-actions {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .footer-note {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }

            .print-actions {
                display: none;
            }

            .info-grid {
                page-break-inside: avoid;
            }

            .products-table {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .invoice-container {
                padding: 20px;
            }

            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .company-info {
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .products-table {
                font-size: 12px;
            }

            .products-table th,
            .products-table td {
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="print-actions">
            <button onclick="window.print()" class="btn">🖨️ In hóa đơn</button>
            <a href="{{ route('client.order.show', $order->code) }}" class="btn btn-secondary">← Quay lại</a>
        </div>

        <div class="invoice-header">
            <div>
                <div class="invoice-title">HÓA ĐƠN BÁN HÀNG</div>
                <div class="invoice-meta">
                    <div><strong>Mã đơn hàng:</strong> {{ $order->code }}</div>
                    <div><strong>Ngày lập:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            <div class="company-info">
                <strong>{{ $settings->site_name ?? 'Bảo Tín JSC' }}</strong>
                @if($settings->contact_address ?? null)
                    <div>{{ $settings->contact_address }}</div>
                @endif
                @if($settings->contact_phone ?? null)
                    <div>Hotline: {{ $settings->contact_phone }}</div>
                @endif
                @if($settings->contact_email ?? null)
                    <div>Email: {{ $settings->contact_email }}</div>
                @endif
                @if($settings->site_tax_code ?? null)
                    <div>MST: {{ $settings->site_tax_code }}</div>
                @endif
            </div>
        </div>

        <div class="invoice-body">
            <div class="info-grid">
                <div class="info-box">
                    <h3>Thông tin khách hàng</h3>
                    <div class="info-item">
                        <strong>Họ tên:</strong>
                        <span>{{ $order->receiver_name }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Điện thoại:</strong>
                        <span>{{ $order->receiver_phone }}</span>
                    </div>
                    @if($order->receiver_email)
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span>{{ $order->receiver_email }}</span>
                        </div>
                    @endif
                    <div class="info-item">
                        <strong>Địa chỉ:</strong>
                        <span>
                            {{ $order->shipping_address }}, 
                            {{ $addressNames['ward'] ?? '' }}, 
                            {{ $addressNames['district'] ?? '' }}, 
                            {{ $addressNames['province'] ?? '' }}
                        </span>
                    </div>
                </div>

                <div class="info-box">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="info-item">
                        <strong>Mã đơn:</strong>
                        <span>{{ $order->code }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Thanh toán:</strong>
                        <span>
                            @if($order->payment_method === 'cod')
                                COD (Thanh toán khi nhận hàng)
                            @else
                                {{ $order->payment_method }}
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>Trạng thái:</strong>
                        <span class="status-badge status-{{ $order->payment_status }}">
                            @if($order->payment_status === 'paid')
                                Đã thanh toán
                            @else
                                Chưa thanh toán
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>Vận chuyển:</strong>
                        <span>{{ strtoupper($order->shipping_partner ?? 'N/A') }}</span>
                    </div>
                </div>
            </div>

            <table class="products-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</strong>
                                @if($item->variant)
                                    <br><small style="color: #666;">{{ $item->variant->name }}</small>
                                @endif
                                @if($item->product && $item->product->sku)
                                    <br><small style="color: #999;">SKU: {{ $item->product->sku }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td class="text-right"><strong>{{ number_format($item->total, 0, ',', '.') }} đ</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                </div>
                @if($order->voucher_discount > 0)
                    <div class="summary-row discount">
                        <span>Giảm giá:</span>
                        <span>-{{ number_format($order->voucher_discount, 0, ',', '.') }} đ</span>
                    </div>
                @endif
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                </div>
                <div class="summary-row total">
                    <span>TỔNG CỘNG:</span>
                    <span>{{ number_format($order->final_price, 0, ',', '.') }} đ</span>
                </div>
            </div>

            @if($order->customer_note)
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 3px solid #667eea;">
                    <strong>Ghi chú:</strong> {{ $order->customer_note }}
                </div>
            @endif

            <div class="footer-note">
                <p>Cảm ơn quý khách đã mua hàng tại {{ $settings->site_name ?? 'Bảo Tín JSC' }}!</p>
                <p>Hóa đơn này có giá trị pháp lý và được sử dụng để tra cứu, đổi trả sản phẩm.</p>
            </div>
        </div>
    </div>
</body>
</html>
