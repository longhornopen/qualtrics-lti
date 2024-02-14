import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    resolve: {
        alias: {
            '~bootstrap': 'bootstrap',
        }
    },
    plugins: [
        laravel([
            'resources/sass/app.scss',
            'resources/js/app.js',
        ]),
        viteStaticCopy({
            targets: [
                { src: 'node_modules/tinymce', dest: '.'},
            ],
            verbose: true,
        }),
    ],
});
