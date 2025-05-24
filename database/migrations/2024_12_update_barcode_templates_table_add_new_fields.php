<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('barcode_templates', function (Blueprint $table) {
            // Add new fields for enhanced functionality
            $table->decimal('gap_horizontal', 8, 2)->default(2)->after('margin_right');
            $table->decimal('gap_vertical', 8, 2)->default(2)->after('gap_horizontal');
            $table->integer('labels_per_page')->default(24)->after('rows_per_page');
            $table->text('template_html')->nullable()->after('text_size');
            $table->text('template_css')->nullable()->after('template_html');
            $table->json('custom_fields')->nullable()->after('fields');
        });
    }

    public function down(): void
    {
        Schema::table('barcode_templates', function (Blueprint $table) {
            $table->dropColumn([
                'gap_horizontal',
                'gap_vertical',
                'labels_per_page',
                'template_html',
                'template_css',
                'custom_fields',
            ]);
        });
    }
};
