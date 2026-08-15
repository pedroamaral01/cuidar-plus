import { defineConfig } from 'cypress';

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
        },
        supportFile: 'cypress/support/component.js',
        specPattern: 'cypress/component/**/*.cy.jsx',
        video: false,
    },
});
