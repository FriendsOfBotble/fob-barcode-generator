<?php

use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('barcode_templates')) {
            return;
        }

        Schema::create('barcode_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('paper_size', 20)->default('A4'); // A4, Letter, P4, thermal
            $table->string('orientation', 20)->default('portrait'); // portrait, landscape
            $table->decimal('label_width')->default(50.00); // in mm
            $table->decimal('label_height')->default(30.00); // in mm
            $table->decimal('margin_top')->default(10.00); // in mm
            $table->decimal('margin_bottom')->default(10.00); // in mm
            $table->decimal('margin_left')->default(10.00); // in mm
            $table->decimal('margin_right')->default(10.00); // in mm
            $table->decimal('gap_horizontal')->default(2.00); // in mm
            $table->decimal('gap_vertical')->default(2.00); // in mm
            $table->decimal('padding')->default(2.00); // in mm
            $table->integer('columns_per_page')->default(4);
            $table->integer('rows_per_page')->default(10);
            $table->integer('labels_per_page')->default(24);
            $table->string('barcode_type', 50)->default(BarcodeTypeEnum::CODE128); // Use enum default
            $table->decimal('barcode_width')->default(40.00); // in mm
            $table->decimal('barcode_height')->default(15.00); // in mm
            $table->boolean('include_text')->default(true);
            $table->string('text_position', 20)->default('bottom'); // top, bottom, none
            $table->integer('text_size')->default(8); // font size in pt
            $table->text('template_html')->nullable(); // Custom HTML template
            $table->text('template_css')->nullable(); // Custom CSS styles
            $table->json('fields')->nullable(); // JSON array of fields to include
            $table->json('custom_fields')->nullable(); // Additional custom fields
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_templates');
    }
};
