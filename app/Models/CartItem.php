<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'uuid',
        'status',
        'quantity',
        'price',
        'total_price',
        'options',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => 'array',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function syncPrice(): void
    {
        $this->loadMissing(['product', 'variant']);

        if (! $this->product) {
            return;
        }

        // Lấy giá từ variant hoặc product
        if ($this->variant && $this->variant->is_active) {
            $resolved = (float) $this->variant->display_price;
        } else {
            $resolved = $this->product->resolveCartPrice();
        }

        if ((float) $this->price !== (float) $resolved) {
            $this->price = $resolved;
            $this->save();
        }
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->price * (int) $this->quantity;
    }

    // ------------------------------
    // Scope
    // ------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
