import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

// Lire le fichier version.json
const version = fs.readFileSync('version.txt', 'utf-8').trim();

export default defineConfig({
    define: {
        'process.env.APP_VERSION': JSON.stringify(version),
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        sourcemap: true,
        chunkSizeWarningLimit: 5000,
    }
});
