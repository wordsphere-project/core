import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/wordsphere.css',
                'resources/css/filament/admin/wordsphere.css',
                'resources/js/wordsphere.js',
            ],
        }),
    ],
})
