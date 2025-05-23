<?php

namespace FriendsOfBotble\BarcodeGenerator\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static BarcodeTypeEnum CODE128()
 * @method static BarcodeTypeEnum CODE39()
 * @method static BarcodeTypeEnum EAN13()
 * @method static BarcodeTypeEnum EAN8()
 * @method static BarcodeTypeEnum UPC_A()
 * @method static BarcodeTypeEnum UPC_E()
 * @method static BarcodeTypeEnum QRCODE()
 * @method static BarcodeTypeEnum DATAMATRIX()
 * @method static BarcodeTypeEnum GTIN()
 */
class BarcodeTypeEnum extends Enum
{
    public const CODE128 = 'CODE128';
    public const CODE39 = 'CODE39';
    public const EAN13 = 'EAN13';
    public const EAN8 = 'EAN8';
    public const UPC_A = 'UPC-A';
    public const UPC_E = 'UPC-E';
    public const QRCODE = 'QRCODE';
    public const DATAMATRIX = 'DATAMATRIX';
    public const GTIN = 'GTIN';

    public static function labels(): array
    {
        return [
            self::CODE128 => 'CODE128 (Recommended)',
            self::CODE39 => 'CODE39',
            self::EAN13 => 'EAN-13',
            self::EAN8 => 'EAN-8',
            self::UPC_A => 'UPC-A',
            self::UPC_E => 'UPC-E',
            self::QRCODE => 'QR Code',
            self::DATAMATRIX => 'DataMatrix',
            self::GTIN => 'GTIN',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    public function toHtml(): HtmlString
    {
        $color = match ($this->value) {
            self::CODE128 => 'success',
            self::QRCODE => 'info',
            self::DATAMATRIX => 'warning',
            default => 'primary',
        };

        return new HtmlString(sprintf(
            '<span class="badge bg-%s">%s</span>',
            $color,
            $this->label()
        ));
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function getDescription(string $value): string
    {
        return match ($value) {
            self::CODE128 => 'Most versatile and widely supported barcode type. Recommended for general use.',
            self::CODE39 => 'Simple alphanumeric barcode, good for basic applications.',
            self::EAN13 => 'Standard retail barcode for products (13 digits).',
            self::EAN8 => 'Compact retail barcode for small products (8 digits).',
            self::UPC_A => 'North American retail standard (12 digits).',
            self::UPC_E => 'Compact UPC for small products (6 digits).',
            self::QRCODE => 'Two-dimensional code that can store large amounts of data.',
            self::DATAMATRIX => 'Compact 2D code ideal for small items.',
            self::GTIN => 'Global Trade Item Number for international trade.',
            default => 'Standard barcode type.',
        };
    }
}
