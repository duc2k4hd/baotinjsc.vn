@extends('admins.layouts.master')

@section('title', 'Cài đặt hệ thống')
@section('page-title', '⚙️ Cài đặt')

@push('head')
    <link rel="shortcut icon" href="{{ asset('admins/img/icons/settings-icon.png') }}" type="image/x-icon">
@endpush

@push('styles')
    <style>
        .settings-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }
        .settings-filters input,
        .settings-filters select {
            padding: 7px 10px;
            border: 1px solid #cbd5f5;
            border-radius: 6px;
            font-size: 13px;
        }
        .settings-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            font-size: 13px;
        }
        .settings-table th,
        .settings-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
        }
        .settings-table th {
            background: #f8fafc;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 12px;
            color: #475569;
        }
        .settings-table tbody tr:hover td {
            background: #f1f5f9;
        }
        .badge {
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 11px;
        }
        .badge-public { background: #dcfce7; color: #15803d; }
        .badge-private { background: #fee2e2; color: #b91c1c; }
        .actions {
            display: flex;
            gap: 6px;
        }
        .maintenance-control-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            border-left: 5px solid #2563eb;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .maintenance-control-card.active {
            border-left-color: #ef4444;
            background: #fffbfa;
        }
        .maintenance-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }
        .maintenance-status-badge.live {
            background: #dcfce7;
            color: #15803d;
        }
        .maintenance-status-badge.maintenance {
            background: #fee2e2;
            color: #b91c1c;
            animation: pulse-danger 2s infinite;
        }
        @keyframes pulse-danger {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
@endpush

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
            <h2 style="margin:0;">Danh sách Settings</h2>
            <p style="margin:4px 0 0;color:#64748b;font-size:14px;">Quản lý toàn bộ cấu hình hệ thống.</p>
        </div>
        <a href="{{ route('admin.settings.create') }}" class="btn btn-primary">➕ Thêm setting</a>
    </div>

    <!-- KHU VỰC BẬT TẮT CHẾ ĐỘ BẢO TRÌ NHANH -->
    <div class="maintenance-control-card {{ $isMaintenance ? 'active' : '' }}">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="font-size: 32px; line-height: 1;">
                {{ $isMaintenance ? '🚧' : '🌐' }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <span style="font-weight: 700; font-size: 15px; color: #1e293b;">Trạng thái Website Khách hàng:</span>
                    @if($isMaintenance)
                        <span class="maintenance-status-badge maintenance">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#dc2626;"></span>
                            ĐANG BẢO TRÌ (Khách truy cập sẽ thấy trang 503)
                        </span>
                    @else
                        <span class="maintenance-status-badge live">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#16a34a;"></span>
                            HOẠT ĐỘNG BÌNH THƯỜNG
                        </span>
                    @endif
                </div>
                <p style="margin: 0; font-size: 13px; color: #64748b;">
                    Hệ thống chặn ngay từ Middleware đầu tiên (HTTP 503 + Retry-After: 1800) không qua Controller để tối đa tốc độ và giữ an toàn chuẩn SEO. Admin vẫn đăng nhập và làm việc bình thường.
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.settings.preview-maintenance') }}" target="_blank" class="btn btn-outline-secondary" style="border: 1px solid #cbd5e1; background: #fff; color: #334155; padding: 7px 14px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
                👁️ Xem trước trang 503
            </a>

            <form action="{{ route('admin.settings.toggle-maintenance') }}" method="POST"
                  onsubmit="return confirm('{{ $isMaintenance ? 'Bạn có chắc chắn muốn TẮT chế độ bảo trì và mở lại website cho khách hàng truy cập?' : 'CẢNH BÁO: Bạn có chắc chắn muốn BẬT chế độ bảo trì? Mọi người dùng khách sẽ bị chặn ngay lập tức và chuyển sang trang bảo trì.' }}');">
                @csrf
                @if($isMaintenance)
                    <button type="submit" class="btn btn-success" style="background:#16a34a;border:none;color:#fff;padding:8px 16px;border-radius:6px;font-weight:600;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <span>✅</span> Tắt bảo trì (Mở lại web)
                    </button>
                @else
                    <button type="submit" class="btn btn-danger" style="background:#dc2626;border:none;color:#fff;padding:8px 16px;border-radius:6px;font-weight:600;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <span>⚠️</span> Bật chế độ bảo trì
                    </button>
                @endif
            </form>
        </div>
    </div>

    <form class="settings-filters" method="GET">
        <input type="text" name="keyword" placeholder="Tìm key hoặc label..."
               value="{{ request('keyword') }}">

        <select name="group">
            <option value="">-- Nhóm --</option>
            @foreach($groups as $group)
                <option value="{{ $group }}" {{ request('group') === $group ? 'selected' : '' }}>
                    {{ $group }}
                </option>
            @endforeach
        </select>

        <select name="type">
            <option value="">-- Kiểu dữ liệu --</option>
            @foreach($types as $type)
                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>

        <select name="is_public">
            <option value="">-- Hiển thị --</option>
            <option value="1" {{ request('is_public') === '1' ? 'selected' : '' }}>Public</option>
            <option value="0" {{ request('is_public') === '0' ? 'selected' : '' }}>Private</option>
        </select>

        <button type="submit" class="btn btn-secondary">Lọc</button>
    </form>

    <div class="table-responsive">
        <table class="settings-table">
            <thead>
            <tr>
                <th>Label / Key</th>
                <th>Nhóm</th>
                <th>Kiểu</th>
                <th>Giá trị</th>
                <th>Public</th>
                <th>Cập nhật</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($settings_all as $setting)
                <tr>
                    <td>
                        <strong>{{ $setting->label ?: '—' }}</strong>
                        <div style="color:#94a3b8;">{{ $setting->key }}</div>
                    </td>
                    <td>{{ $setting->group ?: 'general' }}</td>
                    <td>{{ ucfirst($setting->type) }}</td>
                    <td style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($setting->value), 60) }}
                    </td>
                    <td>
                        @if($setting->is_public)
                            <span class="badge badge-public">Public</span>
                        @else
                            <span class="badge badge-private">Private</span>
                        @endif
                    </td>
                    <td>{{ $setting->updated_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.settings.edit', $setting) }}" class="btn btn-secondary btn-sm">Sửa</a>
                            <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST"
                                  onsubmit="return confirm('Xác nhận xoá setting này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xoá</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">Chưa có setting nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $settings_all->links() }}
    </div>
@endsection


