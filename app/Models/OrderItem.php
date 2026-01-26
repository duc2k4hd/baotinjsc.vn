<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'uuid',
        'quantity',
        'price',
        'total',
        'options',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'options' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }


    /**
     * Backward-compatible accessor cho total_price dùng cột total.
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->attributes['total'] ?? 0);
    }

    public function setTotalPriceAttribute($value): void
    {
        $this->attributes['total'] = $value;
    }
}
