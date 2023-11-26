
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
        'node_modules/jquery/dist/jquery.min.js',
        'public/vendors/metro4/js/metro.js',
        'vendor/enyo/dropzone/dist/dropzone.js',
        'resources/js/utils.js',
        'node_modules/easymde/dist/easymde.min.js',
        ],
    'public/js/all.js')
    .sourceMaps();

mix.styles([
    'public/vendors/chartjs/Chart.css',
    'public/vendors/metro4/css/metro-all.css',
    'resources/css/app.css',
    'vendor/enyo/dropzone/dist/dropzone.css',
    'resources/css/calendar.css',
    'node_modules/easymde/dist/easymde.min.css',
    'node_modules/@fortawesome/fontawesome-free/css/fontawesome.min.css',
    ],
    'public/css/all.css');
