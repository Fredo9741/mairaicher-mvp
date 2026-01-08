import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Minification agressive
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Optimisation des chunks
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
        // Réduire la limite de warning pour les gros fichiers
        chunkSizeWarningLimit: 1000,
        // Optimisation CSS
        cssMinify: true,
    },
});
