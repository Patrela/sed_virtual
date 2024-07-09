import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/stock.css',
                'resources/css/product.css',
                'resources/js/viewFunctions.js',
                // 'resources/js/bootstrap.js',
            ],
            refresh: true,
        }),
    ],
});

/*
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
*/
