const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.setPublicPath('./dist').
   js('resources/assets/admin/js/app.js', 'dist/admin/js')
   .sass('resources/assets/admin/sass/app.scss', 'dist/admin/css')
   .sass('resources/assets/admin/sass/login.scss', 'dist/admin/css')
   .version();