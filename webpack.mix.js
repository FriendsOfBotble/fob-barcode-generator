const mix = require('laravel-mix');

const path = require('path');
const directory = path.basename(path.resolve(__dirname));
const source = `platform/plugins/${directory}`;
const dist = `public/vendor/core/plugins/${directory}`;

mix
    .sass(`${source}/resources/sass/barcode-generator.scss`, `${dist}/css`)
    .js(`${source}/resources/js/barcode-generator.js`, `${dist}/js`);

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/barcode-generator.css`, `${source}/public/css`)
        .copy(`${dist}/js/barcode-generator.js`, `${source}/public/js`)
}
