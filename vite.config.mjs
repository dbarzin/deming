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
            input: [
        // Common
            'resources/css/app.css',
            'resources/css/calendar.css',
            'resources/css/icons.css',
        // Metro 5.0
        //    'node_modules/@olton/metroui/lib/metro.all.css',
        //    'node_modules/@olton/metroui/lib/metro.all.js',
        // Charts
            'node_modules/chart.js/dist/chart.umd.js',
        // DropZone
            'node_modules/dropzone/dist/dropzone.css',
            'node_modules/dropzone/dist/dropzone-min.js',
        // EasyMDE Editor
            'node_modules/easymde/dist/easymde.min.css',
            'node_modules/easymde/dist/easymde.min.js',
        // Moment
            'node_modules/moment/dist/moment.js',
        // Home made
            'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        sourcemap: true, // pour générer tes maps de debug
        chunkSizeWarningLimit: 5000, // Augmente la limite à 5000 KB (5 MB)
    }
});
