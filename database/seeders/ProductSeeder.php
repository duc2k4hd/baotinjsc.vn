<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả categories active
        $categories = Category::where('is_active', true)->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('Không có danh mục nào. Vui lòng chạy CategorySeeder trước.');
            return;
        }

        // Lấy tất cả brands active
        $brands = Brand::where('is_active', true)->get();
        
        if ($brands->isEmpty()) {
            $this->command->warn('Không có brand nào. Đang tạo brand mặc định...');
            $brands = collect([Brand::create([
                'name' => 'Bảo Tín JSC',
                'slug' => 'bao-tin-jsc',
                'is_active' => true,
            ])]);
        }

        // Tạo một số ảnh mẫu (sẽ dùng no-image.webp trong thực tế)
        $this->command->info('Đang tạo sản phẩm cho ' . $categories->count() . ' danh mục...');

        $productCount = 0;
        $variantCount = 0;

        foreach ($categories as $category) {
            // Mỗi danh mục tạo từ 5-15 sản phẩm
            $productsPerCategory = rand(5, 15);
            
            $this->command->info("Đang tạo {$productsPerCategory} sản phẩm cho danh mục: {$category->name}");

            for ($i = 0; $i < $productsPerCategory; $i++) {
                // Random brand
                $brand = $brands->random();

                // Tạo tên sản phẩm phù hợp với danh mục
                $productName = $this->generateProductName($category->name, $brand->name);

                // Tạo product
                $product = Product::factory()->create([
                    'name' => $productName,
                    'slug' => Str::slug($productName) . '-' . rand(1000, 9999),
                    'primary_category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'category_ids' => [$category->id],
                    'is_featured' => rand(1, 10) <= 2, // 20% featured
                ]);

                // Tạo 1-3 ảnh cho mỗi sản phẩm
                $imageIds = [];
                $imageCount = rand(1, 3);
                
                for ($j = 0; $j < $imageCount; $j++) {
                    $image = Image::create([
                        'url' => 'no-image.webp', // Sử dụng ảnh mặc định
                        'title' => $product->name . ' - Hình ' . ($j + 1),
                        'alt' => $product->name,
                        'is_primary' => $j === 0,
                        'order' => $j,
                    ]);
                    $imageIds[] = $image->id;
                }

                $product->update(['image_ids' => $imageIds]);

                // 30% sản phẩm có variants
                if (rand(1, 10) <= 3) {
                    $variantCountForProduct = rand(2, 5);
                    
                    for ($k = 0; $k < $variantCountForProduct; $k++) {
                        $variant = ProductVariant::factory()->create([
                            'product_id' => $product->id,
                            'name' => $this->getVariantName($k),
                            'sort_order' => $k,
                        ]);
                        $variantCount++;
                    }
                }

                $productCount++;
            }
        }

        $this->command->info("✅ Đã tạo thành công:");
        $this->command->info("   - {$productCount} sản phẩm");
        $this->command->info("   - {$variantCount} biến thể");
        $this->command->info("   - Cho {$categories->count()} danh mục");
    }

    /**
     * Tạo tên sản phẩm phù hợp với danh mục
     */
    private function generateProductName(string $categoryName, string $brandName): string
    {
        $categoryLower = strtolower($categoryName);
        
        // Từ khóa sản phẩm theo danh mục
        $productTypes = [
            'cảm biến' => ['Cảm biến tiệm cận', 'Cảm biến quang điện', 'Cảm biến nhiệt độ', 'Cảm biến áp suất', 'Cảm biến độ ẩm', 'Cảm biến siêu âm', 'Cảm biến từ tính'],
            'cảm biến tiệm cận' => ['Cảm biến tiệm cận quang điện', 'Cảm biến tiệm cận từ tính', 'Cảm biến tiệm cận siêu âm', 'Cảm biến tiệm cận laser'],
            'cảm biến quang' => ['Cảm biến quang phản xạ', 'Cảm biến quang truyền qua', 'Cảm biến quang laser', 'Cảm biến quang fiber'],
            'cảm biến nhiệt' => ['Cảm biến nhiệt độ PT100', 'Cảm biến nhiệt độ K-type', 'Cảm biến nhiệt độ hồng ngoại', 'Cảm biến nhiệt độ RTD'],
            'cảm biến áp suất' => ['Cảm biến áp suất tuyệt đối', 'Cảm biến áp suất tương đối', 'Cảm biến áp suất chênh lệch', 'Cảm biến áp suất chân không'],
            'relay' => ['Relay trung gian', 'Relay thời gian', 'Relay bảo vệ', 'Relay điều khiển', 'Relay SSR', 'Relay công suất'],
            'contactor' => ['Contactor 3 pha', 'Contactor 1 pha', 'Contactor chân không', 'Contactor từ tính'],
            'biến tần' => ['Biến tần 1 pha', 'Biến tần 3 pha', 'Biến tần vector', 'Biến tần servo'],
            'motor' => ['Motor bước', 'Motor servo', 'Motor DC', 'Motor AC', 'Motor đồng bộ', 'Motor không đồng bộ'],
            'encoder' => ['Encoder quang học', 'Encoder từ tính', 'Encoder tuyệt đối', 'Encoder tương đối'],
            'hmi' => ['Màn hình HMI 7 inch', 'Màn hình HMI 10 inch', 'Màn hình HMI 15 inch', 'Màn hình HMI cảm ứng'],
            'plc' => ['PLC compact', 'PLC modular', 'PLC chuyên dụng', 'PLC công nghiệp'],
            'sensor' => ['Sensor quang', 'Sensor từ tính', 'Sensor siêu âm', 'Sensor laser', 'Sensor hồng ngoại'],
        ];

        // Tìm loại sản phẩm phù hợp
        $types = null;
        foreach ($productTypes as $key => $values) {
            if (str_contains($categoryLower, $key)) {
                $types = $values;
                break;
            }
        }

        // Nếu không tìm thấy, dùng danh sách mặc định
        if (!$types) {
            $types = [
                'Thiết bị tự động hóa',
                'Module điều khiển',
                'Bộ điều khiển',
                'Thiết bị đo lường',
                'Cảm biến công nghiệp',
                'Bộ chuyển đổi tín hiệu',
                'Bộ khuếch đại tín hiệu',
                'Thiết bị giám sát',
            ];
        }

        $type = $types[array_rand($types)];
        
        // Model/Series
        $models = ['Series', 'Model', 'Type', 'Series Pro', 'Series Plus', 'Series Elite'];
        $model = $models[array_rand($models)] . ' ' . rand(100, 9999);

        // Đặc điểm kỹ thuật
        $specs = [
            rand(12, 24) . 'VDC',
            rand(100, 240) . 'VAC',
            rand(1, 10) . 'A',
            rand(10, 100) . 'W',
            'IP' . rand(54, 68),
            'NPN',
            'PNP',
            'NO/NC',
        ];
        $spec = (rand(1, 3) <= 2) ? ' - ' . $specs[array_rand($specs)] : '';

        return $type . ' ' . $brandName . ' ' . $model . $spec;
    }

    /**
     * Lấy tên variant
     */
    private function getVariantName(int $index): string
    {
        $names = [
            'Size S',
            'Size M',
            'Size L',
            'Size XL',
            'Màu Đỏ',
            'Màu Xanh',
            'Màu Vàng',
            'Màu Trắng',
            'Màu Đen',
            'Loại A',
            'Loại B',
            'Loại C',
            'Phiên bản Standard',
            'Phiên bản Pro',
            'Phiên bản Premium',
            'Công suất 100W',
            'Công suất 200W',
            'Công suất 500W',
            'Điện áp 12V',
            'Điện áp 24V',
            'Điện áp 220V',
        ];

        return $names[$index % count($names)] ?? "Variant " . ($index + 1);
    }
}
