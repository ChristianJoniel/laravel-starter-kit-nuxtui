import { createInertiaApp } from '@inertiajs/vue3';
import '../css/app.css';
import ui from '@nuxt/ui/vue-plugin';

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
