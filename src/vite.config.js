import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// ============================================================
//  WAYNA — Vite config
//  Configurado para funcionar dentro del contenedor Docker
//  con Hot Module Replacement (HMR) hacia el host.
// ============================================================

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

    server: {
        // Escucha en todas las interfaces dentro del contenedor
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,

        // El HMR se dirige al navegador del host (tu máquina)
        hmr: {
            host: 'host.docker.internal',
            port: 5173,
        },
    },
});
