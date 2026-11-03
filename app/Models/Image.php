<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'images';

    protected $fillable = [
        'name',
        'product_id',
        'entity_type',
        'entity_id',
        'role',
        'title',
        'notes',
        'alt',
        'url',
        'path',
        'extension',
        'mime_type',
        'size',
        'width',
        'height',
        'context',
        'file_modified_at',
        'thumbnail_url',
        'medium_url',
        'is_primary',
        'order',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'entity_id' => 'integer',
        'is_primary' => 'boolean',
        'order' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'file_modified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('media_missing_file_ids');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('media_missing_file_ids');
        });
    }

    /** Scope lọc theo context (post / product) */
    public function scopeForContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    /** Trả về mảng dimensions hoặc null */
    public function getDimensionsAttribute(): ?array
    {
        if ($this->width && $this->height) {
            return ['width' => $this->width, 'height' => $this->height];
        }
        return null;
    }

    public function getNameAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        $path = $this->attributes['path'] ?? $this->attributes['url'] ?? null;
        return $path ? basename((string) $path) : null;
    }

    /**
     * Accessor for the raw image file name / relative path stored in DB.
     */
    public function getUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return $value;
    }
}
