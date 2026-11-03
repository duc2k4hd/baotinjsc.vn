<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('images')) {
            return;
        }

        Schema::table('images', function (Blueprint $table) {
            if (! Schema::hasColumn('images', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (! Schema::hasColumn('images', 'entity_type')) {
                $table->string('entity_type', 50)->nullable()->after('name')->index();
            }
            if (! Schema::hasColumn('images', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type')->index();
            }
            if (! Schema::hasColumn('images', 'role')) {
                $table->string('role', 50)->nullable()->after('entity_id')->index();
            }
            if (! Schema::hasColumn('images', 'path')) {
                $table->string('path', 191)->nullable()->after('url')->index();
            }
            if (! Schema::hasColumn('images', 'extension')) {
                $table->string('extension', 10)->nullable()->after('path');
            }
            if (! Schema::hasColumn('images', 'mime_type')) {
                $table->string('mime_type', 191)->nullable()->after('extension');
            }
            if (! Schema::hasColumn('images', 'size')) {
                $table->unsignedBigInteger('size')->default(0)->after('mime_type');
            }
            if (! Schema::hasColumn('images', 'width')) {
                $table->unsignedInteger('width')->nullable()->after('size');
            }
            if (! Schema::hasColumn('images', 'height')) {
                $table->unsignedInteger('height')->nullable()->after('width');
            }
            if (! Schema::hasColumn('images', 'context')) {
                $table->string('context', 50)->default('product')->after('height')->index();
            }
            if (! Schema::hasColumn('images', 'file_modified_at')) {
                $table->timestamp('file_modified_at')->nullable()->after('context');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('images')) {
            return;
        }

        Schema::table('images', function (Blueprint $table) {
            $cols = ['name', 'entity_type', 'entity_id', 'role', 'path', 'extension', 'mime_type', 'size', 'width', 'height', 'context', 'file_modified_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('images', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

