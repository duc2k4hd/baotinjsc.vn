@extends('admins.layouts.master')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', '📦 Chi tiết đơn hàng')

@push('head')
    <link rel="shortcut icon" href="{{ asset('admins/img/icons/order-icon.png') }}" type="image/x-icon">
@endpush

@push('styles')
    <style>
        .order-detail-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }
        
        .order-detail-main {
            min-width: 0;
        }
        
        .order-detail-sidebar {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        
        .sidebar-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        
        .sidebar-card h4 {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .sidebar-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .sidebar-actions .btn {
            width: 100%;
            justify-content: center;
            font-size: 13px;
            padding: 8px 12px;
        }
        
        .sidebar-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .sidebar-info-item:last-child {
            border-bottom: none;
        }
        
        .sidebar-info-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .sidebar-info-value {
            font-size: 13px;
            color: #1f2937;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }
        
        .sidebar-status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        @media (max-width: 1200px) {
            .order-detail-layout {
                grid-template-columns: 1fr;
            }
            
            .order-detail-sidebar {
                position: relative;
                top: 0;
                max-height: none;
            }
        }
        
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
        .info-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:12px;
        }
        .info-item {
            padding:8px 0;
            border-bottom:1px solid #eef2f7;
        }
        .info-label {
            font-size:12px;
            color:#64748b;
            margin-bottom:4px;
        }
        .info-value {
            font-size:14px;
            font-weight:500;
            color:#0f172a;
        }
        .items-table {
            width:100%;
            border-collapse:collapse;
            background:#fff;
        }
        .items-table th, .items-table td {
            padding:10px;
            border-bottom:1px solid #eef2f7;
            text-align:left;
            font-size:13px;
        }
        .items-table th {
            background:#f8fafc;
            font-weight:600;
            color:#475569;
        }
        .product-image {
            width:60px;
            height:60px;
            object-fit:cover;
            border-radius:6px;
        }
        .badge {
            padding:3px 9px;
            border-radius:999px;
            font-size:11px;
            font-weight:600;
        }
        .badge-pending { background:#fef3c7;color:#92400e;}
        .badge-processing { background:#dbeafe;color:#1d4ed8;}
        .badge-completed { background:#dcfce7;color:#15803d;}
        .badge-cancelled { background:#fee2e2;color:#b91c1c;}
        .badge-paid { background:#dcfce7;color:#15803d;}
        .badge-failed { background:#fee2e2;color:#b91c1c;}
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
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="margin:0;">Đơn hàng: {{ $order->code }}</h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">↩️ Quay lại</a>
        </div>

        <div class="order-detail-layout">
            <!-- Cột trái: Nội dung chính -->
            <div class="order-detail-main">

        <div class="card">
            <h3>Thông tin đơn hàng</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Mã đơn hàng</div>
                    <div class="info-value"><strong>{{ $order->code }}</strong></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Người đặt</div>
                    <div class="info-value">
                        @if($order->account)
                            {{ $order->account->name ?? $order->account->email }}
                        @else
                            Khách ({{ substr($order->session_id ?? '', 0, 16) }}...)
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $order->status }}">
                            @if($order->status === 'pending') Chờ xử lý
                            @elseif($order->status === 'processing') Đang xử lý
                            @elseif($order->status === 'completed') Hoàn thành
                            @else Đã hủy
                            @endif
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Thanh toán</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $order->payment_status }}">
                            @if($order->payment_status === 'pending') Chờ thanh toán
                            @elseif($order->payment_status === 'paid') Đã thanh toán
                            @else Thất bại
                            @endif
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Vận chuyển</div>
                    <div class="info-value">
                        @if($order->delivery_status === 'pending') Chờ giao
                        @elseif($order->delivery_status === 'shipped') Đang giao hàng
                        @elseif($order->delivery_status === 'delivered') Đã giao hàng
                        @elseif($order->delivery_status === 'cancelled') Đã hủy hàng
                        @else Đã trả hàng
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày tạo</div>
                    <div class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cập nhật</div>
                    <div class="info-value">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

                <div class="card">
                    <h3>Thông tin người nhận</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Họ tên</div>
                    <div class="info-value">{{ $order->receiver_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value">{{ $order->receiver_phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->receiver_email ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Địa chỉ</div>
                    <div class="info-value">
                        @if($order->shippingAddress)
                            {{ $order->shippingAddress->detail_address }}<br>
                            {{ $order->shippingAddress->ward }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->province }}
                        @elseif($order->shipping_address)
                            {{ $order->shipping_address }}<br>
                            @php
                                $addressParts = array_filter([
                                    $addressNames['ward'] ?? null,
                                    $addressNames['district'] ?? null,
                                    $addressNames['province'] ?? null,
                                ]);
                            @endphp
                            @if(!empty($addressParts))
                                {{ implode(', ', $addressParts) }}
                            @else
                                <span style="color:#94a3b8;font-size:12px;">(ID: {{ $order->shipping_ward_id ?? '' }}{{ $order->shipping_ward_id && ($order->shipping_district_id || $order->shipping_province_id) ? ', ' : '' }}{{ $order->shipping_district_id ?? '' }}{{ $order->shipping_district_id && $order->shipping_province_id ? ', ' : '' }}{{ $order->shipping_province_id ?? '' }})</span>
                            @endif
                        @else
                            Chưa có địa chỉ giao hàng
                        @endif
                    </div>
                </div>
            </div>
        </div>

                <div class="card">
                    <h3>Thanh toán & Vận chuyển</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Phương thức</div>
                    <div class="info-value">
                        @if($order->payment_method === 'cod') COD
                        @elseif($order->payment_method === 'qr') QR Code
                        @elseif($order->payment_method === 'momo') MoMo
                        @elseif($order->payment_method === 'zalopay') ZaloPay
                        @elseif($order->payment_method === 'payos') PayOS
                        @else {{ $order->payment_method }}
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Mã giao dịch</div>
                    <div class="info-value">{{ $order->transaction_code ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Đơn vị vận chuyển</div>
                    <div class="info-value">{{ ucfirst($order->shipping_partner) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Mã vận đơn</div>
                    <div class="info-value">{{ $order->shipping_tracking_code ?? '—' }}</div>
                </div>
            </div>
        </div>

                <div class="card">
                    <h3>Sản phẩm trong đơn ({{ $order->items->count() }})</h3>
            <div class="table-responsive">
                <table class="items-table">
                    <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>
                                @php
                                    $imageUrl = $item->variant?->primaryVariantImage
                                        ? asset('clients/assets/img/clothes/' . $item->variant->primaryVariantImage->url)
                                        : ($item->product->primaryImage
                                            ? asset('clients/assets/img/clothes/' . $item->product->primaryImage->url)
                                            : asset('clients/assets/img/clothes/no-image.webp'));
                                @endphp
                                <img src="{{ $imageUrl }}" alt="" class="product-image">
                            </td>
                            <td>
                                <strong>{{ $item->product->name }}</strong><br>
                                <small style="color:#64748b;">SKU: {{ $item->product->sku }}</small>
                            </td>
                            <td>
                                @if($item->variant)
                                    @php
                                        $attrs = is_string($item->variant->attributes)
                                            ? json_decode($item->variant->attributes, true)
                                            : $item->variant->attributes;
                                        $attrs = is_array($attrs) ? $attrs : [];
                                    @endphp
                                    @forelse($attrs as $key => $value)
                                        <span style="font-size:11px;color:#64748b;">{{ ucfirst($key) }}: {{ $value }}</span><br>
                                    @empty
                                        <span style="color:#94a3b8;">—</span>
                                    @endforelse
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->quantity) }}</td>
                            <td>{{ number_format($item->price) }} đ</td>
                            <td><strong>{{ number_format($item->total_price) }} đ</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:20px;color:#64748b;">Không có sản phẩm</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

                <div class="card">
                    <h3>Tổng tiền</h3>
            <div>
                <div class="summary-item">
                    <span>Tổng tiền sản phẩm:</span>
                    <strong>{{ number_format($order->total_price) }} đ</strong>
                </div>
                <div class="summary-item">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee) }} đ</span>
                </div>
                <div class="summary-item">
                    <span>Thuế:</span>
                    <span>{{ number_format($order->tax) }} đ</span>
                </div>
                <div class="summary-item">
                    <span>Giảm giá:</span>
                    <span>-{{ number_format($order->discount) }} đ</span>
                </div>
                @if($order->voucher_discount > 0)
                <div class="summary-item">
                    <span>Giảm giá voucher ({{ $order->voucher_code ?? 'N/A' }}):</span>
                    <span>-{{ number_format($order->voucher_discount) }} đ</span>
                </div>
                @endif
                <div class="summary-item total">
                    <span>Thành tiền:</span>
                    <strong>{{ number_format($order->final_price) }} đ</strong>
                </div>
            </div>
        </div>

                @if($order->customer_note || $order->admin_note)
                    <div class="card">
                        <h3>Ghi chú</h3>
                        @if($order->customer_note)
                        <div style="margin-bottom:12px;">
                            <div class="info-label">Ghi chú khách hàng:</div>
                            <div style="padding:8px;background:#f8fafc;border-radius:6px;font-size:13px;">{{ $order->customer_note }}</div>
                        </div>
                        @endif
                        @if($order->admin_note)
                        <div>
                            <div class="info-label">Ghi chú nội bộ:</div>
                            <div style="padding:8px;background:#fef3c7;border-radius:6px;font-size:13px;">{{ $order->admin_note }}</div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Cột phải: Sidebar với actions và quick info -->
            <div class="order-detail-sidebar">
                <!-- Quick Info -->
                <div class="sidebar-card">
                    <h4>Thông tin nhanh</h4>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Mã đơn:</span>
                        <span class="sidebar-info-value">{{ $order->code }}</span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Trạng thái:</span>
                        <span class="sidebar-info-value">
                            <span class="badge badge-{{ $order->status }}">
                                @if($order->status === 'pending') Chờ xử lý
                                @elseif($order->status === 'processing') Đang xử lý
                                @elseif($order->status === 'completed') Hoàn thành
                                @else Đã hủy
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Thanh toán:</span>
                        <span class="sidebar-info-value">
                            <span class="badge badge-{{ $order->payment_status }}">
                                @if($order->payment_status === 'pending') Chờ thanh toán
                                @elseif($order->payment_status === 'paid') Đã thanh toán
                                @else Thất bại
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Vận chuyển:</span>
                        <span class="sidebar-info-value">
                            @if($order->delivery_status === 'pending') Chờ giao
                            @elseif($order->delivery_status === 'shipped') Đang giao
                            @elseif($order->delivery_status === 'delivered') Đã giao
                            @elseif($order->delivery_status === 'cancelled') Đã hủy
                            @else Đã trả
                            @endif
                        </span>
                    </div>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Tổng tiền:</span>
                        <span class="sidebar-info-value" style="color:#15803d;font-size:14px;">
                            <strong>{{ number_format($order->final_price) }} đ</strong>
                        </span>
                    </div>
                    @if($order->shipping_tracking_code)
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-label">Mã vận đơn:</span>
                        <span class="sidebar-info-value" style="font-size:12px;color:#1d4ed8;">
                            {{ $order->shipping_tracking_code }}
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="sidebar-card">
                    <h4>Thao tác</h4>
                    <div class="sidebar-actions">
                        @if(!in_array($order->status, ['completed', 'cancelled']))
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">✏️ Sửa đơn hàng</a>
                        @endif
                        @if($order->status === 'pending')
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" style="margin:0;" onsubmit="return confirm('Bạn có chắc muốn chấp nhận đơn hàng này?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="btn btn-success">✅ Chấp nhận đơn hàng</button>
                            </form>
                        @endif
                        @if($order->status === 'processing' && $order->delivery_status === 'delivered')
                            <form action="{{ route('admin.orders.complete', $order) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">✅ Hoàn thành</button>
                            </form>
                        @endif
                        @if($order->canCancel())
                            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" style="margin:0;" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">❌ Hủy đơn</button>
                            </form>
                        @endif
                        @if($order->status === 'completed' && $order->delivery_status === 'delivered' && $order->payment_status === 'paid')
                            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-info">🧾 In hóa đơn</a>
                            <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn btn-success">⬇️ PDF hóa đơn</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
</script>
@endpush

