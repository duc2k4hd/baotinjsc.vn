@php
    $isEdit = $emailAccount->exists;
@endphp

<form action="{{ $isEdit ? route('admin.email-accounts.update', $emailAccount) : route('admin.email-accounts.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px;">
        <a href="{{ route('admin.email-accounts.index') }}" class="btn btn-secondary">↩️ Quay lại danh sách</a>
        <button type="submit" class="btn btn-primary">💾 {{ $isEdit ? 'Cập nhật' : 'Tạo' }} email</button>
    </div>

    <div class="card">
        <h3>Thông tin email</h3>
        <div class="grid-2">
            <div>
                <label>Địa chỉ email <span style="color: red;">*</span></label>
                <input type="email" name="email" class="form-control" 
                       value="{{ old('email', $emailAccount->email) }}" 
                       required 
                       placeholder="info@baotinjsc.vn">
                @error('email')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label>Tên hiển thị <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $emailAccount->name) }}" 
                       required 
                       placeholder="Info, Support, Sales...">
                @error('name')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div style="margin-top: 16px;">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3" 
                      placeholder="Mô tả về email này...">{{ old('description', $emailAccount->description) }}</textarea>
        </div>
        <div class="grid-3" style="margin-top: 16px;">
            <div>
                <label>
                    <input type="checkbox" name="is_default" value="1" 
                           {{ old('is_default', $emailAccount->is_default) ? 'checked' : '' }}>
                    Đặt làm email mặc định
                </label>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                    Email mặc định sẽ được sử dụng khi không chọn email cụ thể
                </div>
            </div>
            <div>
                <label>
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $emailAccount->is_active ?? true) ? 'checked' : '' }}>
                    Đang hoạt động
                </label>
            </div>
            <div>
                <label>Thứ tự hiển thị</label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $emailAccount->order ?? 0) }}" 
                       min="0" 
                       placeholder="0">
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>Cấu hình SMTP</h3>
        <div style="margin-bottom: 16px; padding: 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; color: #0369a1;">
            <strong>💡 Lưu ý:</strong> Nếu để trống, hệ thống sẽ sử dụng cấu hình mặc định từ file .env (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION). Chỉ điền nếu muốn dùng cấu hình riêng cho email này.
        </div>
        <div class="grid-2">
            <div>
                <label>SMTP Host</label>
                <input type="text" name="mail_host" class="form-control" 
                       value="{{ old('mail_host', $emailAccount->mail_host) }}" 
                       placeholder="{{ config('mail.mailers.smtp.host') ?? 'smtp.gmail.com' }}">
                <small style="font-size: 11px; color: #64748b;">Để trống = dùng {{ config('mail.mailers.smtp.host') ?? 'MAIL_HOST từ .env' }}</small>
            </div>
            <div>
                <label>SMTP Port</label>
                <input type="number" name="mail_port" class="form-control" 
                       value="{{ old('mail_port', $emailAccount->mail_port) }}" 
                       placeholder="{{ config('mail.mailers.smtp.port') ?? '587' }}"
                       min="1" max="65535">
                <small style="font-size: 11px; color: #64748b;">Để trống = dùng {{ config('mail.mailers.smtp.port') ?? 'MAIL_PORT từ .env' }}</small>
            </div>
        </div>
        <div class="grid-2" style="margin-top: 16px;">
            <div>
                <label>SMTP Username</label>
                <input type="text" name="mail_username" class="form-control" 
                       value="{{ old('mail_username', $emailAccount->mail_username) }}" 
                       placeholder="{{ config('mail.mailers.smtp.username') ?? 'your-email@gmail.com' }}">
                <small style="font-size: 11px; color: #64748b;">Để trống = dùng {{ config('mail.mailers.smtp.username') ? 'giá trị từ .env' : 'MAIL_USERNAME từ .env' }}</small>
            </div>
            <div>
                <label>SMTP Password</label>
                <input type="password" name="mail_password" class="form-control" 
                       value="" 
                       placeholder="Nhập mật khẩu mới (để trống nếu không đổi)">
                <small style="font-size: 11px; color: #64748b;">
                    @if($isEdit && $emailAccount->mail_password)
                        Đã có mật khẩu. Nhập mới để thay đổi.
                    @else
                        Để trống = dùng {{ config('mail.mailers.smtp.password') ? 'giá trị từ .env' : 'MAIL_PASSWORD từ .env' }}
                    @endif
                </small>
            </div>
        </div>
        <div style="margin-top: 16px;">
            <label>Mã hóa (Encryption)</label>
            <select name="mail_encryption" class="form-control">
                <option value="">-- Dùng mặc định từ .env --</option>
                <option value="tls" {{ old('mail_encryption', $emailAccount->mail_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ old('mail_encryption', $emailAccount->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
            </select>
            <small style="font-size: 11px; color: #64748b;">Để trống = dùng giá trị từ .env (thường là tls)</small>
        </div>
    </div>
</form>

