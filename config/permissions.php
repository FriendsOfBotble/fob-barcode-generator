<?php

return [
    [
        'name' => 'Barcode Generator',
        'flag' => 'barcode-generator.index',
    ],
    [
        'name' => 'Generate Barcodes',
        'flag' => 'barcode-generator.generate',
        'parent_flag' => 'barcode-generator.index',
    ],
    [
        'name' => 'Print Labels',
        'flag' => 'barcode-generator.print',
        'parent_flag' => 'barcode-generator.index',
    ],
    [
        'name' => 'Manage Templates',
        'flag' => 'barcode-generator.templates',
        'parent_flag' => 'barcode-generator.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'barcode-generator.settings',
        'parent_flag' => 'barcode-generator.index',
    ],
];
