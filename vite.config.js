import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/publico/home.css',
                'resources/css/consulta/estado.css',
                'resources/css/consulta/consulta-form.css',
                'resources/css/consulta/status.css',
                'resources/css/consulta/enviarcomprovativo.css',
                'resources/css/pedidos/etapa1-dados-pessoais.css',
                'resources/css/pedidos/etapa2-dados-profissionais.css',
                'resources/css/pedidos/etapa3-upload-documentos.css',
                'resources/css/pedidos/etapa4-ficha-cobranca.css',
            ],
            refresh: true,
        }),
    ],
});