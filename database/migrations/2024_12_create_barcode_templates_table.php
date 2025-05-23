<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('paper_size', 20)->default('A4'); // A4, Letter, P4, thermal
            $table->string('orientation', 20)->default('portrait'); // portrait, landscape
            $table->decimal('label_width', 8, 2)->default(50.00); // in mm
            $table->decimal('label_height', 8, 2)->default(30.00); // in mm
            $table->decimal('margin_top', 8, 2)->default(10.00); // in mm
            $table->decimal('margin_bottom', 8, 2)->default(10.00); // in mm
            $table->decimal('margin_left', 8, 2)->default(10.00); // in mm
            $table->decimal('margin_right', 8, 2)->default(10.00); // in mm
            $table->decimal('padding', 8, 2)->default(2.00); // in mm
            $table->integer('columns_per_page')->default(4);
            $table->integer('rows_per_page')->default(10);
            $table->string('barcode_type', 50)->default('C128'); // C128, EAN13, QR, etc.
            $table->decimal('barcode_width', 8, 2)->default(40.00); // in mm
            $table->decimal('barcode_height', 8, 2)->default(15.00); // in mm
            $table->boolean('include_text')->default(true);
            $table->string('text_position', 20)->default('bottom'); // top, bottom, none
            $table->integer('text_size')->default(8); // font size in pt
            $table->json('fields')->nullable(); // JSON array of fields to include
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
