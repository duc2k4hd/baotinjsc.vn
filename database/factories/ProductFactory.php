<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3, false);
        $slug = Str::slug($name);
        $price = $this->faker->randomFloat(2, 100000, 50000000);
        $hasSale = $this->faker->boolean(30); // 30% có sale
        $salePrice = $hasSale ? $price * (1 - $this->faker->randomFloat(2, 0.1, 0.5)) : null;

        return [
            'sku' => 'SP-' . strtoupper(Str::random(8)),
            'name' => $name,
            'slug' => $slug . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraphs(5, true),
            'short_description' => $this->faker->sentence(10),
            'price' => $price,
            'sale_price' => $salePrice,
            'cost_price' => $price * 0.6, // Giá vốn = 60% giá bán
            'stock_quantity' => $this->faker->numberBetween(0, 1000), // Sản phẩm luôn có số lượng (variant mới có thể null)
            'meta_title' => $name . ' - ' . $this->faker->company(),
            'meta_description' => $this->faker->sentence(15),
            'meta_keywords' => implode(', ', $this->faker->words(5)), // String với các từ cách nhau dấu phẩy
            'meta_canonical' => null,
            'primary_category_id' => null, // Sẽ set trong seeder
            'brand_id' => null, // Sẽ set trong seeder
            'category_included_ids' => null,
            'category_ids' => null, // Sẽ set trong seeder
            'tag_ids' => null,
            'image_ids' => null, // Sẽ set trong seeder
            'link_catalog' => null,
            'video_url' => $this->faker->boolean(10) ? 'https://www.youtube.com/watch?v=' . Str::random(11) : null,
            'is_featured' => $this->faker->boolean(20), // 20% là featured
            'is_active' => true,
            'created_by' => 1,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? $this->faker->randomFloat(2, 100000, 50000000);
            $discount = $this->faker->randomFloat(2, 0.1, 0.5);
            return [
                'sale_price' => $price * (1 - $discount),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product has high stock.
     */
    public function highStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(500, 10000),
        ]);
    }
}
