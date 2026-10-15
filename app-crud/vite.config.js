// Vite registers CSS/JavaScript entry files with the Laravel plugin and refreshes development
// pages. npm run build creates public/build/manifest.json used by the vite directive.

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
