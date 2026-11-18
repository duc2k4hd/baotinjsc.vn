<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'description',
        'image_desktop',
        'image_mobile',
        'link',
        'target',
        'position',
        'order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->orderBy('order', 'asc');
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('start_at')
                    ->orWhereNotNull('end_at');
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('end_at')
            ->where('end_at', '<', now());
    }

    /**
     * Chuẩn hóa và trả về URL ảnh banner hoạt động trên cả môi trường local (public/) và cPanel (public_html/).
     */
    public function resolveBannerImageUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $clean = trim(str_replace('\\', '/', $value));
        if ($clean === '') {
            return null;
        }

        if (Str::startsWith($clean, ['http://', 'https://'])) {
            return $clean;
        }

        $folder = trim(config('banners.image.path', 'clients/assets/img/banners'), '/');
        $clean = ltrim($clean, '/');

        // Bỏ prefix thừa nếu trong DB đã lưu cả đường dẫn thư mục
        if (Str::startsWith($clean, 'public/')) {
            $clean = substr($clean, 7);
        }
        if (Str::startsWith($clean, $folder.'/')) {
            $filename = substr($clean, strlen($folder) + 1);
        } elseif (Str::startsWith($clean, 'clients/assets/img/')) {
            return asset($clean);
        } else {
            $filename = $clean;
        }

        $relativePath = $folder.'/'.$filename;
        $publicFile = public_path($relativePath);
        $rootFile = base_path($relativePath);

        // Nếu đang chạy trên hosting mà Document Root là base_path (public_html)
        // và file mới chỉ nằm trong public/clients/... thì tự động đồng bộ ra ngoài hoặc trả về URL đúng
        if ($publicFile !== $rootFile && is_file($publicFile) && ! is_file($rootFile)) {
            $docRoot = ! empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
            $baseRoot = realpath(base_path());

            if ($docRoot && $baseRoot && $docRoot === $baseRoot) {
                $rootDir = dirname($rootFile);
                if (! is_dir($rootDir)) {
                    @mkdir($rootDir, 0755, true);
                }
                @copy($publicFile, $rootFile);
                @chmod($rootFile, 0644);

                if (! is_file($rootFile)) {
                    return asset('public/'.$relativePath);
                }
            }
        }

        return asset($relativePath);
    }

    // Accessors
    public function getImageDesktopUrlAttribute(): string
    {
        return $this->resolveBannerImageUrl($this->image_desktop) ?? '';
    }

    public function getImageMobileUrlAttribute(): ?string
    {
        return $this->resolveBannerImageUrl($this->image_mobile);
    }

    public function getStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_scheduled) {
            return 'scheduled';
        }

        return 'active';
    }

    public function getIsScheduledAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_at && $this->start_at->gt($now)) {
            return true;
        }

        if ($this->end_at && $this->end_at->gt($now)) {
            return true;
        }

        return false;
    }

    public function getIsExpiredAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->end_at && $this->end_at->lt(now());
    }

    // Backward compatibility: get image attribute returns image_desktop
    public function getImageAttribute(): ?string
    {
        return $this->image_desktop;
    }

    // Methods
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_at && $this->start_at->gt($now)) {
            return false;
        }

        if ($this->end_at && $this->end_at->lt(now)) {
            return false;
        }

        return true;
    }

    public static function getNextOrderForPosition(string $position): int
    {
        $maxOrder = static::where('position', $position)
            ->max('order');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Xóa cache banners khi banner được cập nhật hoặc xóa.
     */
    protected static function booted(): void
    {
        static::saved(function (self $banner) {
            Cache::forget('banners_home_parent');
            Cache::forget('banners_home_children');
        });

        static::deleted(function (self $banner) {
            Cache::forget('banners_home_parent');
            Cache::forget('banners_home_children');
        });
    }
}
