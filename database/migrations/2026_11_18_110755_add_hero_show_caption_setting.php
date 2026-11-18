<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm setting hero_show_caption nếu chưa tồn tại
        $exists = DB::table('settings')->where('key', 'hero_show_caption')->exists();
        if (! $exists) {
            DB::table('settings')->insert([
                'key'         => 'hero_show_caption',
                'value'       => '0',
                'type'        => 'boolean',
                'group'       => 'banner',
                'label'       => 'Hiển thị tiêu đề & mô tả trên Hero Slider',
                'description' => 'Bật để hiện tiêu đề, mô tả và nút CTA lên slider. Tắt để slider chỉ hiển thị ảnh thuần.',
                'is_public'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        Cache::forget('settings');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'hero_show_caption')->delete();
        Cache::forget('settings');
    }
};
