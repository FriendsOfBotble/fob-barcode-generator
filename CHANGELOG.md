# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-12-19

### Added
- Initial release of FOB Barcode Generator plugin
- Support for multiple barcode types (Code 128, EAN-13, EAN-8, UPC-A, UPC-E)
- Customizable label templates with various paper sizes
- A4, Letter, P4, and thermal printer support
- Product integration with barcode generation from SKU or barcode field
- Admin interface for generating and printing barcode labels
- Bulk barcode generation for multiple products
- Template management system with default templates
- Configurable label content (name, SKU, price, brand, etc.)
- Print preview functionality
- Settings page for default configurations
- Multi-language support (English, Vietnamese)
- Product page integration with barcode preview
- Admin meta box for product barcode display

### Features
- **Barcode Types**: Code 128, EAN-13, EAN-8, UPC-A, UPC-E
- **Paper Formats**: A4, Letter, P4 labels, Thermal 4x6, Thermal 2x1
- **Label Content**: Product name, SKU, barcode, price, sale price, brand, category, attributes
- **Print Options**: Individual labels, bulk printing, PDF download
- **Template System**: Create, edit, and manage custom label templates
- **Integration**: Seamless integration with Botble ecommerce products

### Technical
- Built on Botble CMS 7.5.0+
- **Self-contained barcode generation library** - no external dependencies!
- Responsive admin interface
- Modern JavaScript and SCSS assets
- Comprehensive permission system
- Database migrations and seeders included

### Requirements
- PHP 8.1+
- Botble CMS 7.5.0+

### Installation
1. Extract plugin to `platform/plugins/fob-barcode-generator`
2. Activate plugin in admin panel
3. Configure settings as needed

**No composer commands required!** The plugin is completely self-contained.
