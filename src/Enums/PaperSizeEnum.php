<?php

namespace FriendsOfBotble\BarcodeGenerator\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PaperSizeEnum A4()
 * @method static PaperSizeEnum LETTER()
 * @method static PaperSizeEnum THERMAL_4X6()
 * @method static PaperSizeEnum THERMAL_4X3()
 * @method static PaperSizeEnum THERMAL_2X1()
 * @method static PaperSizeEnum CUSTOM()
 */
class PaperSizeEnum extends Enum
{
    public const A4 = 'A4';
    public const LETTER = 'LETTER';
    public const THERMAL_4X6 = 'THERMAL_4X6';
    public const THERMAL_4X3 = 'THERMAL_4X3';
    public const THERMAL_2X1 = 'THERMAL_2X1';
    public const CUSTOM = 'CUSTOM';

    public static function labels(): array
    {
        return [
            self::A4 => 'A4 (210 × 297 mm)',
            self::LETTER => 'Letter (8.5 × 11 inch)',
            self::THERMAL_4X6 => 'Thermal 4×6 inch',
            self::THERMAL_4X3 => 'Thermal 4×3 inch',
            self::THERMAL_2X1 => 'Thermal 2×1 inch',
            self::CUSTOM => 'Custom Size',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    public function toHtml(): HtmlString
    {
        $color = match ($this->value) {
            self::A4 => 'primary',
            self::LETTER => 'info',
            self::THERMAL_4X6, self::THERMAL_4X3, self::THERMAL_2X1 => 'warning',
            self::CUSTOM => 'secondary',
            default => 'light',
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

    public static function getDimensions(string $value): array
    {
        return match ($value) {
            self::A4 => ['width' => 210, 'height' => 297, 'unit' => 'mm'],
            self::LETTER => ['width' => 8.5, 'height' => 11, 'unit' => 'inch'],
            self::THERMAL_4X6 => ['width' => 4, 'height' => 6, 'unit' => 'inch'],
            self::THERMAL_4X3 => ['width' => 4, 'height' => 3, 'unit' => 'inch'],
            self::THERMAL_2X1 => ['width' => 2, 'height' => 1, 'unit' => 'inch'],
            default => ['width' => 0, 'height' => 0, 'unit' => 'mm'],
        };
    }

    public static function getLabelsPerPage(string $value): int
    {
        return match ($value) {
            self::A4 => 24, // Default 24 labels per A4 page
            self::LETTER => 30, // Default 30 labels per Letter page
            self::THERMAL_4X6 => 1, // Single label for thermal
            self::THERMAL_4X3 => 1,
            self::THERMAL_2X1 => 1,
            default => 1,
        };
    }

    public static function getDescription(string $value): string
    {
        return match ($value) {
            self::A4 => 'Standard A4 paper size, suitable for office printers. Supports multiple labels per page.',
            self::LETTER => 'US Letter paper size, suitable for office printers. Supports multiple labels per page.',
            self::THERMAL_4X6 => 'Large thermal label, ideal for shipping labels and product tags.',
            self::THERMAL_4X3 => 'Medium thermal label, good for product labels and inventory tags.',
            self::THERMAL_2X1 => 'Small thermal label, perfect for small product labels and price tags.',
            self::CUSTOM => 'Define your own custom paper dimensions.',
            default => 'Standard paper size.',
        };
    }
}
