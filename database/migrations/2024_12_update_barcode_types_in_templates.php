<?php

use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        // Update existing barcode types to use new enum values
        $legacyMappings = [
            'C128' => BarcodeTypeEnum::CODE128,
            'CODE128' => BarcodeTypeEnum::CODE128,
            'C39' => BarcodeTypeEnum::CODE39,
            'CODE39' => BarcodeTypeEnum::CODE39,
            'EAN13' => BarcodeTypeEnum::EAN13,
            'EAN8' => BarcodeTypeEnum::EAN8,
            'UPCA' => BarcodeTypeEnum::UPC_A,
            'UPC-A' => BarcodeTypeEnum::UPC_A,
            'UPCE' => BarcodeTypeEnum::UPC_E,
            'UPC-E' => BarcodeTypeEnum::UPC_E,
            'QR' => BarcodeTypeEnum::QRCODE,
            'QRCODE' => BarcodeTypeEnum::QRCODE,
            'DATAMATRIX' => BarcodeTypeEnum::DATAMATRIX,
            'GTIN' => BarcodeTypeEnum::GTIN,
        ];

        foreach ($legacyMappings as $oldType => $newType) {
            DB::table('barcode_templates')
                ->where('barcode_type', $oldType)
                ->update(['barcode_type' => $newType]);
        }

        // Set default barcode type for templates that don't have one
        DB::table('barcode_templates')
            ->whereNull('barcode_type')
            ->orWhere('barcode_type', '')
            ->update(['barcode_type' => BarcodeTypeEnum::CODE128]);
    }

    public function down(): void
    {
        // Reverse the mapping for rollback
        $reverseMappings = [
            BarcodeTypeEnum::CODE128 => 'C128',
            BarcodeTypeEnum::CODE39 => 'C39',
            BarcodeTypeEnum::EAN13 => 'EAN13',
            BarcodeTypeEnum::EAN8 => 'EAN8',
            BarcodeTypeEnum::UPC_A => 'UPCA',
            BarcodeTypeEnum::UPC_E => 'UPCE',
            BarcodeTypeEnum::QRCODE => 'QR',
            BarcodeTypeEnum::DATAMATRIX => 'DATAMATRIX',
            BarcodeTypeEnum::GTIN => 'GTIN',
        ];

        foreach ($reverseMappings as $newType => $oldType) {
            DB::table('barcode_templates')
                ->where('barcode_type', $newType)
                ->update(['barcode_type' => $oldType]);
        }
    }
};
