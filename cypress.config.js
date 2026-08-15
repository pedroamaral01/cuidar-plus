import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'cypress';
import { fileURLToPath } from 'node:url';

export default defineConfig({
    e2e: {
        // Dentro do compose o Cypress fala com o serviço nginx; fora dele,
        // sobrescreva com CYPRESS_baseUrl=http://localhost.
        baseUrl: process.env.CYPRESS_baseUrl ?? 'http://localhost',
        supportFile: 'cypress/support/e2e.js',
        specPattern: 'cypress/e2e/**/*.cy.js',
        video: false,
        screenshotOnRunFailure: true,
        viewportWidth: 1280,
        viewportHeight: 800,
        defaultCommandTimeout: 10000,
    },

    component: {
        devServer: {
            framework: 'react',
            bundler: 'vite',
            /*
             * Config própria, sem o plugin do laravel-vite: ele recusa subir o
             * servidor de HMR em ambiente de CI, e aqui não há Blade nem
             * manifest envolvidos — apenas o componente React isolado. O alias
             * "@", que normalmente vem daquele plugin, é declarado à mão.
             */
            viteConfig: {
                plugins: [react(), tailwindcss()],
                resolve: {
                    alias: {
                        '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
                    },
                },
            },
        },
        supportFile: 'cypress/support/component.js',
        specPattern: 'cypress/component/**/*.cy.jsx',
        video: false,
        viewportWidth: 500,
        viewportHeight: 400,
    },
});
