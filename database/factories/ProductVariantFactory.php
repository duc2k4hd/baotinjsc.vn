<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 50000, 10000000);
        $hasSale = $this->faker->boolean(30);
        $salePrice = $hasSale ? $price * (1 - $this->faker->randomFloat(2, 0.1, 0.4)) : null;

        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->randomElement([
                'Size S',
                'Size M',
                'Size L',
                'Size XL',
                'Màu Đỏ',
                'Màu Xanh',
                'Màu Vàng',
                'Loại A',
                'Loại B',
                'Loại C',
                'Phiên bản Standard',
                'Phiên bản Pro',
                'Phiên bản Premium',
            ]),
            'sku' => 'VAR-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'price' => $price,
            'sale_price' => $salePrice,
            'cost_price' => $price * 0.6,
            'stock_quantity' => $this->faker->boolean(20) ? null : $this->faker->numberBetween(0, 500),
            'image_id' => null, // Sẽ set trong seeder nếu cần
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'note' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
        ];
    }

    /**
     * Indicate that the variant is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? $this->faker->randomFloat(2, 50000, 10000000);
            $discount = $this->faker->randomFloat(2, 0.1, 0.4);
            return [
                'sale_price' => $price * (1 - $discount),
            ];
        });
    }

    /**
     * Indicate that the variant is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }
}
