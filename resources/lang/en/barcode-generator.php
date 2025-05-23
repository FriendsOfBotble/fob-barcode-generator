<?php

return [
    'name' => 'Barcode Generator',
    'description' => 'Generate and print barcode labels for products & orders',

    'menu' => [
        'barcode_generator' => 'Barcode Generator',
        'generate' => 'Generate Barcodes',
        'templates' => 'Label Templates',
        'print' => 'Print Labels',
    ],

    'settings' => [
        'title' => 'Barcode Generator Settings',
        'description' => 'Configure barcode generation and label printing settings',
        'default_barcode_type' => 'Default Barcode Type',
        'default_barcode_width' => 'Default Barcode Width (mm)',
        'default_barcode_height' => 'Default Barcode Height (mm)',
        'include_text' => 'Include Text Below Barcode',
        'text_position' => 'Text Position',
        'text_size' => 'Text Size (pt)',
        'label_settings' => 'Label Settings',
        'label_width' => 'Label Width (mm)',
        'label_height' => 'Label Height (mm)',
        'label_margin' => 'Label Margin (mm)',
        'label_padding' => 'Label Padding (mm)',
        'paper_settings' => 'Paper Settings',
        'paper_size' => 'Paper Size',
        'orientation' => 'Orientation',
        'columns_per_page' => 'Columns per Page',
        'rows_per_page' => 'Rows per Page',
    ],

    'barcode_types' => [
        'C128' => 'Code 128',
        'EAN13' => 'EAN-13',
        'EAN8' => 'EAN-8',
        'UPCA' => 'UPC-A',
        'UPCE' => 'UPC-E',
        'QR' => 'QR Code',
        'DATAMATRIX' => 'Data Matrix',
    ],

    'paper_sizes' => [
        'A4' => 'A4 (210 x 297 mm)',
        'Letter' => 'Letter (8.5 x 11 in)',
        'P4' => 'P4 Label (4 x 6 in)',
        'thermal_4x6' => 'Thermal 4x6 in',
        'thermal_2x1' => 'Thermal 2x1 in',
        'custom' => 'Custom Size',
    ],

    'orientations' => [
        'portrait' => 'Portrait',
        'landscape' => 'Landscape',
    ],

    'text_positions' => [
        'top' => 'Top',
        'bottom' => 'Bottom',
        'none' => 'None',
    ],

    'fields' => [
        'name' => 'Product Name',
        'sku' => 'SKU',
        'barcode' => 'Barcode',
        'price' => 'Price',
        'sale_price' => 'Sale Price',
        'brand' => 'Brand',
        'category' => 'Category',
        'attributes' => 'Attributes',
    ],

    'templates' => [
        'name' => 'Template Name',
        'description' => 'Description',
        'is_default' => 'Default Template',
        'is_active' => 'Active',
        'create' => 'Create Template',
        'edit' => 'Edit Template',
        'delete' => 'Delete Template',
        'duplicate' => 'Duplicate Template',
    ],

    'generate' => [
        'title' => 'Generate Barcodes',
        'select_products' => 'Select Products',
        'select_template' => 'Select Template',
        'quantity' => 'Quantity per Product',
        'preview' => 'Preview',
        'download_pdf' => 'Download PDF',
        'print' => 'Print',
    ],

    'messages' => [
        'template_created' => 'Template created successfully!',
        'template_updated' => 'Template updated successfully!',
        'template_deleted' => 'Template deleted successfully!',
        'barcode_generated' => 'Barcode generated successfully!',
        'no_products_selected' => 'Please select at least one product.',
        'no_template_selected' => 'Please select a template.',
    ],

    'no_barcode_data' => 'No barcode or SKU data available for this product.',
    'barcode_value' => 'Barcode: :value',
    'sku_value' => 'SKU: :value',
    'print_label' => 'Print Label',
    'generate_barcode' => 'Generate Barcode',
    'generation_error' => 'Error generating barcode',
];
