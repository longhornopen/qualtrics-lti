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

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .extract();
mix.copy('node_modules/tinymce/themes', 'public/js/themes');
mix.copy('node_modules/tinymce/plugins', 'public/js/plugins');
mix.copy('node_modules/tinymce/skins', 'public/js/skins');

if (mix.inProduction()) {
    mix.version();
}
