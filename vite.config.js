import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
    host: 'localhost',
    port: 4001,
    strictPort: true,
    hmr: false,  // ← deshabilita HMR completamente
    watch: {
        ignored: ['**/storage/framework/views/**'],
    },
},
});