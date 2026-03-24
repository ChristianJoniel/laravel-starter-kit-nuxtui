import { createInertiaApp } from '@inertiajs/vue3';
import { configureEcho } from '@laravel/echo-vue';
import ui from '@nuxt/ui/vue-plugin';
import '../css/app.css';

configureEcho({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT),
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
});

const appName = import.meta.env.VITE_APP_NAME || 'Nuxt UI Starter Kit';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    defaults: {
        visitOptions: (href, options) => ({
            preserveScroll: options?.preserveScroll ?? 'errors',
            ...options,
        }),
    },
    withApp(app) {
        app.use(ui);
    },
});
