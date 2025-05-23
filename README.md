# FOB Barcode Generator

A comprehensive barcode generator plugin for Botble CMS that allows you to generate and print barcode labels for products and orders.

## Features

✅ **Multiple Barcode Types**
- Code 128
- EAN-13
- EAN-8
- UPC-A
- UPC-E

✅ **Flexible Label Templates**
- Customizable label sizes
- Multiple paper formats (A4, Letter, P4, Thermal)
- Configurable margins and padding
- Text positioning options

✅ **Print Support**
- A4 and Letter paper sizes
- P4 label sheets
- Thermal printer labels (4x6, 2x1)
- Bulk printing capabilities

✅ **Product Integration**
- Generate barcodes from product SKU or barcode field
- Bulk barcode generation
- Product page integration
- Admin meta box display

✅ **Customizable Content**
- Product name
- SKU
- Barcode
- Price and sale price
- Brand and category
- Product attributes

## Installation

1. Extract the plugin to `platform/plugins/fob-barcode-generator`
2. Activate the plugin in Admin Panel > Plugins
3. Configure settings in Admin Panel > Settings > Others > Barcode Generator

**No composer commands required!** The plugin is completely self-contained.

## Usage

### Generating Barcodes

1. Go to Admin Panel > Barcode Generator
2. Select products you want to generate barcodes for
3. Choose a label template
4. Set quantity per product
5. Preview or download the labels

### Managing Templates

1. Go to Admin Panel > Barcode Generator > Templates
2. Create custom templates with your preferred settings
3. Set paper size, label dimensions, and content fields
4. Mark templates as default for quick access

### Product Integration

- View barcode preview in product edit page
- Quick generate button in product list
- Automatic barcode type detection

## Requirements

- PHP 8.1+
- Botble CMS 7.5.0+

## Dependencies

- **None!** The plugin includes its own barcode generation library

## License

MIT License

## Support

For support and documentation, visit [Friends of Botble](https://friendsofbotble.com)
