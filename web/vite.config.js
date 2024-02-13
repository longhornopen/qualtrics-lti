import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});

/*
mix.copy('node_modules/tinymce/themes', 'public/js/themes');
mix.copy('node_modules/tinymce/plugins', 'public/js/plugins');
mix.copy('node_modules/tinymce/skins', 'public/js/skins');
 */
