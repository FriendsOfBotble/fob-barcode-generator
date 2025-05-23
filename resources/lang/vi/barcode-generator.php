<?php

return [
    'name' => 'Tạo mã vạch',
    'description' => 'Tạo và in nhãn mã vạch cho sản phẩm & đơn hàng',
    
    'menu' => [
        'barcode_generator' => 'Tạo mã vạch',
        'generate' => 'Tạo mã vạch',
        'templates' => 'Mẫu nhãn',
        'print' => 'In nhãn',
    ],

    'settings' => [
        'title' => 'Cài đặt tạo mã vạch',
        'description' => 'Cấu hình tạo mã vạch và cài đặt in nhãn',
        'default_barcode_type' => 'Loại mã vạch mặc định',
        'default_barcode_width' => 'Chiều rộng mã vạch mặc định (mm)',
        'default_barcode_height' => 'Chiều cao mã vạch mặc định (mm)',
        'include_text' => 'Bao gồm văn bản bên dưới mã vạch',
        'text_position' => 'Vị trí văn bản',
        'text_size' => 'Kích thước văn bản (pt)',
        'label_settings' => 'Cài đặt nhãn',
        'label_width' => 'Chiều rộng nhãn (mm)',
        'label_height' => 'Chiều cao nhãn (mm)',
        'label_margin' => 'Lề nhãn (mm)',
        'label_padding' => 'Khoảng cách nhãn (mm)',
        'paper_settings' => 'Cài đặt giấy',
        'paper_size' => 'Kích thước giấy',
        'orientation' => 'Hướng',
        'columns_per_page' => 'Số cột mỗi trang',
        'rows_per_page' => 'Số hàng mỗi trang',
    ],

    'barcode_types' => [
        'C128' => 'Code 128',
        'EAN13' => 'EAN-13',
        'EAN8' => 'EAN-8',
        'UPCA' => 'UPC-A',
        'UPCE' => 'UPC-E',
        'QR' => 'Mã QR',
        'DATAMATRIX' => 'Data Matrix',
    ],

    'paper_sizes' => [
        'A4' => 'A4 (210 x 297 mm)',
        'Letter' => 'Letter (8.5 x 11 in)',
        'P4' => 'Nhãn P4 (4 x 6 in)',
        'thermal_4x6' => 'Nhiệt 4x6 in',
        'thermal_2x1' => 'Nhiệt 2x1 in',
        'custom' => 'Kích thước tùy chỉnh',
    ],

    'orientations' => [
        'portrait' => 'Dọc',
        'landscape' => 'Ngang',
    ],

    'text_positions' => [
        'top' => 'Trên',
        'bottom' => 'Dưới',
        'none' => 'Không',
    ],

    'fields' => [
        'name' => 'Tên sản phẩm',
        'sku' => 'SKU',
        'barcode' => 'Mã vạch',
        'price' => 'Giá',
        'sale_price' => 'Giá khuyến mãi',
        'brand' => 'Thương hiệu',
        'category' => 'Danh mục',
        'attributes' => 'Thuộc tính',
    ],

    'templates' => [
        'name' => 'Tên mẫu',
        'description' => 'Mô tả',
        'is_default' => 'Mẫu mặc định',
        'is_active' => 'Kích hoạt',
        'create' => 'Tạo mẫu',
        'edit' => 'Chỉnh sửa mẫu',
        'delete' => 'Xóa mẫu',
        'duplicate' => 'Nhân bản mẫu',
    ],

    'generate' => [
        'title' => 'Tạo mã vạch',
        'select_products' => 'Chọn sản phẩm',
        'select_template' => 'Chọn mẫu',
        'quantity' => 'Số lượng mỗi sản phẩm',
        'preview' => 'Xem trước',
        'download_pdf' => 'Tải PDF',
        'print' => 'In',
    ],

    'messages' => [
        'template_created' => 'Tạo mẫu thành công!',
        'template_updated' => 'Cập nhật mẫu thành công!',
        'template_deleted' => 'Xóa mẫu thành công!',
        'barcode_generated' => 'Tạo mã vạch thành công!',
        'no_products_selected' => 'Vui lòng chọn ít nhất một sản phẩm.',
        'no_template_selected' => 'Vui lòng chọn một mẫu.',
    ],

    'no_barcode_data' => 'Không có dữ liệu mã vạch hoặc SKU cho sản phẩm này.',
    'barcode_value' => 'Mã vạch: :value',
    'sku_value' => 'SKU: :value',
    'print_label' => 'In nhãn',
    'generate_barcode' => 'Tạo mã vạch',
    'generation_error' => 'Lỗi tạo mã vạch',
];
