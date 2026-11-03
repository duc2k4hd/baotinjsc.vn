<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('content')->comment('Link hoặc tên ảnh chính');
            }
            if (! Schema::hasColumn('posts', 'thumbnail_alt_text')) {
                $table->string('thumbnail_alt_text')->nullable()->after('thumbnail')->comment('Alt text ảnh chính cho SEO');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'thumbnail_alt_text')) {
                $table->dropColumn('thumbnail_alt_text');
            }
            if (Schema::hasColumn('posts', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};

