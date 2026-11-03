<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('images')) {
            Schema::table('images', function (Blueprint $table) {
                if (!Schema::hasColumn('images', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->index()->after('id');
                }
                if (!Schema::hasColumn('images', 'thumbnail_url')) {
                    $table->string('thumbnail_url', 500)->nullable()->after('url');
                }
                if (!Schema::hasColumn('images', 'medium_url')) {
                    $table->string('medium_url', 500)->nullable()->after('thumbnail_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('images')) {
            Schema::table('images', function (Blueprint $table) {
                if (Schema::hasColumn('images', 'product_id')) {
                    $table->dropColumn('product_id');
                }
                if (Schema::hasColumn('images', 'thumbnail_url')) {
                    $table->dropColumn('thumbnail_url');
                }
                if (Schema::hasColumn('images', 'medium_url')) {
                    $table->dropColumn('medium_url');
                }
            });
        }
    }
};

