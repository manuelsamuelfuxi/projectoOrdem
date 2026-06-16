import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/consulta/status.css',
                'resources/css/consulta/enviarcomprovativo.css' // <-- Novo arquivo aqui
            ],
            refresh: true,
        }),
    ],
});