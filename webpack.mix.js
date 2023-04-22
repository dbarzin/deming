
let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix.js([
        'public/vendors/chartjs/Chart.js',
        'public/vendors/ckeditor/ckeditor.js',
        'public/vendors/jquery/jquery-3.6.1.min.js',
        'public/vendors/metro4/js/metro.js',
        'vendor/enyo/dropzone/dist/dropzone.js',
        'resources/js/utils.js',
        ], 
    'public/js/all.js')
    .sourceMaps();

mix.styles([
    'public/vendors/chartjs/Chart.css', 
    'public/vendors/metro4/css/metro-all.css',
    'resources/css/app.css',
    'vendor/enyo/dropzone/dist/dropzone.css',
    'resources/css/calendar.css',
    ], 
    'public/css/all.css');

