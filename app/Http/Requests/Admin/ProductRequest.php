<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_canonical' => ['nullable', 'string', 'max:500'],
            'primary_category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_included_ids' => ['nullable', 'array'],
            'category_included_ids.*' => ['integer', 'exists:categories,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'tag_names' => ['nullable', 'string'],
            'image_ids' => ['nullable', 'array'],
            'image_ids.*' => ['integer', 'exists:images,id'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'created_by' => ['nullable', 'integer', 'exists:accounts,id'],

            // Catalog & Video
            'link_catalog' => ['nullable'], // chuỗi (cách nhau bằng dấu phẩy/ xuống dòng) hoặc mảng
            'catalog_files' => ['nullable', 'array'],
            'catalog_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:20480'], // 20MB
            'video_url' => ['nullable', 'string', 'max:500'],

            // Primary image
            'primary_image' => ['nullable', 'array'],
            'primary_image.id' => ['nullable', 'integer', 'exists:images,id'],
            'primary_image.existing_path' => ['nullable', 'string'],
            'primary_image.path' => ['nullable', 'string'],
            'primary_image.url' => ['nullable', 'string'],
            'primary_image.title' => ['nullable', 'string', 'max:255'],
            'primary_image.alt' => ['nullable', 'string', 'max:255'],
            'primary_image.file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif,avif', 'max:10240'],

            // Images data
            'images' => ['nullable', 'array'],
            'images.*.id' => ['nullable', 'integer', 'exists:images,id'],
            'images.*.url' => ['nullable', 'string'],
            'images.*.existing_path' => ['nullable', 'string'],
            'images.*.path' => ['nullable', 'string'],
            'images.*.title' => ['nullable', 'string', 'max:255'],
            'images.*.notes' => ['nullable', 'string'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'images.*.order' => ['nullable', 'integer', 'min:0'],
            'images.*.file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif,avif', 'max:10240'],

            // Variants data
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'variants.*.note' => ['nullable', 'string', 'max:500'],
            'variants.*.image_id' => ['nullable', 'integer', 'exists:images,id'],
            'variants.*.image_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug đã tồn tại.',
            'price.required' => 'Giá bán là bắt buộc.',
            'price.numeric' => 'Giá bán phải là số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá bán.',
            'cost_price.numeric' => 'Giá vốn phải là số.',
            'cost_price.min' => 'Giá vốn không được nhỏ hơn 0.',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'primary_category_id.exists' => 'Danh mục chính không tồn tại.',
            'category_ids.*.exists' => 'Một trong các danh mục không tồn tại.',
            'tag_ids.*.exists' => 'Một trong các tag không tồn tại.',
            'image_ids.*.exists' => 'Một trong các ảnh không tồn tại.',
            'created_by.exists' => 'Người tạo không tồn tại.',
            'primary_image.required' => 'Bắt buộc phải chọn ảnh đại diện cho sản phẩm.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasPrimary = !empty($this->input('primary_image.existing_path'))
                || !empty($this->input('primary_image.path'))
                || !empty($this->input('primary_image.id'))
                || $this->hasFile('primary_image.file');

            $hasGallery = false;
            $images = $this->input('images');
            if (is_array($images)) {
                foreach ($images as $img) {
                    if (!empty($img['existing_path']) || !empty($img['path']) || !empty($img['id']) || (isset($img['file']) && $img['file'])) {
                        $hasGallery = true;
                        break;
                    }
                }
            }

            if (!$hasPrimary && !$hasGallery) {
                $validator->errors()->add('primary_image', 'Bắt buộc phải chọn ảnh đại diện cho sản phẩm (hoặc thêm ít nhất một ảnh vào thư viện ảnh).');
            }
        });
    }
}
