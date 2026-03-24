import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import inertia from '@inertiajs/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import ui from '@nuxt/ui/vite'


export default defineConfig(({ command }) => ({
    // Expose the project root to the client so SourceLinks can open files in VSCode during development
    define: command === 'serve' ? { __PROJECT_ROOT__: JSON.stringify(process.cwd()) } : {},
    build: {
        minify: false,
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        inertia(),
        ui({
            autoImport: true,
            inertia: true,
            components: {
              dirs: ['resources/js/components'],
            },
            ui: {
              colors: {
                primary: 'teal',
                neutral: 'gray',
              },
            },
          }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            patterns: ['app/**/*.php', 'routes/**/*.php'],
        }),
    ],
}));
