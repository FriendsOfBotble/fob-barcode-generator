<?php

namespace FriendsOfBotble\BarcodeGenerator\Libraries;

class BarcodeGenerator
{
    protected array $code128 = [
        ' ' => '11011001100', '!' => '11001101100', '"' => '11001100110', '#' => '10010011000',
        '$' => '10010001100', '%' => '10001001100', '&' => '10011001000', "'" => '10011000100',
        '(' => '10001100100', ')' => '11001001000', '*' => '11001000100', '+' => '11000100100',
        ',' => '10110011100', '-' => '10011011100', '.' => '10011001110', '/' => '10111001100',
        '0' => '10011101100', '1' => '10011100110', '2' => '11001110010', '3' => '11001011100',
        '4' => '11001001110', '5' => '11011100100', '6' => '11001110100', '7' => '11101101110',
        '8' => '11101001100', '9' => '11100101100', ':' => '11100100110', ';' => '11101100100',
        '<' => '11100110100', '=' => '11100110010', '>' => '11011011000', '?' => '11011000110',
        '@' => '11000110110', 'A' => '10100011000', 'B' => '10001011000', 'C' => '10001000110',
        'D' => '10110001000', 'E' => '10001101000', 'F' => '10001100010', 'G' => '11010001000',
        'H' => '11000101000', 'I' => '11000100010', 'J' => '10110111000', 'K' => '10110001110',
        'L' => '10001101110', 'M' => '10111011000', 'N' => '10111000110', 'O' => '10001110110',
        'P' => '11101110110', 'Q' => '11010001110', 'R' => '11000101110', 'S' => '11011101000',
        'T' => '11011100010', 'U' => '11011101110', 'V' => '11101011000', 'W' => '11101000110',
        'X' => '11100010110', 'Y' => '11101101000', 'Z' => '11101100010', '[' => '11100011010',
        '\\' => '11101111010', ']' => '11001000010', '^' => '11110001010', '_' => '10100110000',
        '`' => '10100001100', 'a' => '10010110000', 'b' => '10010000110', 'c' => '10000101100',
        'd' => '10000100110', 'e' => '10110010000', 'f' => '10110000100', 'g' => '10011010000',
        'h' => '10011000010', 'i' => '10000110100', 'j' => '10000110010', 'k' => '11000010010',
        'l' => '11001010000', 'm' => '11110111010', 'n' => '11000010100', 'o' => '10001111010',
        'p' => '10100111100', 'q' => '10010111100', 'r' => '10010011110', 's' => '10111100100',
        't' => '10011110100', 'u' => '10011110010', 'v' => '11110100100', 'w' => '11110010100',
        'x' => '11110010010', 'y' => '11011011110', 'z' => '11011110110', '{' => '11110110110',
        '|' => '10101111000', '}' => '10100011110', '~' => '10001011110',
    ];

    public function generateCode128(string $data): string
    {
        $bars = '11010010000'; // Start B

        foreach (str_split($data) as $char) {
            if (isset($this->code128[$char])) {
                $bars .= $this->code128[$char];
            }
        }

        $bars .= '1100011101011'; // Stop

        return $bars;
    }

    public function generateEAN13(string $data): string
    {
        // Ensure 13 digits, calculate checksum if needed
        $data = preg_replace('/[^0-9]/', '', $data);

        if (strlen($data) == 12) {
            // Calculate checksum
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($data[$i]) * ($i % 2 === 0 ? 1 : 3);
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $data .= $checksum;
        }

        $data = str_pad(substr($data, 0, 13), 13, '0', STR_PAD_LEFT);

        $leftOdd = [
            '0001101', '0011001', '0010011', '0111101', '0100011',
            '0110001', '0101111', '0111011', '0110111', '0001011',
        ];

        $leftEven = [
            '0100111', '0110011', '0011011', '0100001', '0011101',
            '0111001', '0000101', '0010001', '0001001', '0010111',
        ];

        $right = [
            '1110010', '1100110', '1101100', '1000010', '1011100',
            '1001110', '1010000', '1000100', '1001000', '1110100',
        ];

        $pattern = [
            '000000', '001011', '001101', '001110', '010011',
            '011001', '011100', '010101', '010110', '011010',
        ];

        $bars = '101'; // Start

        $firstDigit = intval($data[0]);
        $patternCode = $pattern[$firstDigit];

        // Left side
        for ($i = 1; $i <= 6; $i++) {
            $digit = intval($data[$i]);
            if ($patternCode[$i - 1] === '0') {
                $bars .= $leftOdd[$digit];
            } else {
                $bars .= $leftEven[$digit];
            }
        }

        $bars .= '01010'; // Center

        // Right side
        for ($i = 7; $i <= 12; $i++) {
            $digit = intval($data[$i]);
            $bars .= $right[$digit];
        }

        $bars .= '101'; // End

        return $bars;
    }

    public function generateEAN8(string $data): string
    {
        // Ensure 8 digits, calculate checksum if needed
        $data = preg_replace('/[^0-9]/', '', $data);

        if (strlen($data) == 7) {
            // Calculate checksum
            $sum = 0;
            for ($i = 0; $i < 7; $i++) {
                $sum += intval($data[$i]) * ($i % 2 === 0 ? 3 : 1);
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $data .= $checksum;
        }

        $data = str_pad(substr($data, 0, 8), 8, '0', STR_PAD_LEFT);

        $left = [
            '0001101', '0011001', '0010011', '0111101', '0100011',
            '0110001', '0101111', '0111011', '0110111', '0001011',
        ];

        $right = [
            '1110010', '1100110', '1101100', '1000010', '1011100',
            '1001110', '1010000', '1000100', '1001000', '1110100',
        ];

        $bars = '101'; // Start

        // Left side (4 digits)
        for ($i = 0; $i < 4; $i++) {
            $digit = intval($data[$i]);
            $bars .= $left[$digit];
        }

        $bars .= '01010'; // Center

        // Right side (4 digits)
        for ($i = 4; $i < 8; $i++) {
            $digit = intval($data[$i]);
            $bars .= $right[$digit];
        }

        $bars .= '101'; // End

        return $bars;
    }

    public function generateSVG(string $bars, int $width = 200, int $height = 50): string
    {
        $barWidth = $width / strlen($bars);
        $svg = '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">';

        $x = 0;
        foreach (str_split($bars) as $bar) {
            if ($bar === '1') {
                $svg .= '<rect x="' . $x . '" y="0" width="' . $barWidth . '" height="' . $height . '" fill="black"/>';
            }
            $x += $barWidth;
        }

        $svg .= '</svg>';

        return $svg;
    }

    public function generateBarcode(string $data, string $type = 'CODE128'): string
    {
        switch (strtoupper($type)) {
            case 'EAN13':
                return $this->generateEAN13($data);
            case 'EAN8':
                return $this->generateEAN8($data);
            case 'QRCODE':
                // QR codes are handled separately in the service layer
                throw new \InvalidArgumentException('QR codes should be generated using the service layer');
            case 'CODE128':
            default:
                return $this->generateCode128($data);
        }
    }

    public function generateBarcodeSVG(string $data, string $type = 'CODE128', int $width = 200, int $height = 50): string
    {
        $bars = $this->generateBarcode($data, $type);

        return $this->generateSVG($bars, $width, $height);
    }
}
