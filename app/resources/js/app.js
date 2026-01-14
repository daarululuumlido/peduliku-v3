import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useThemeStore } from '@/stores/useThemeStore';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create Pinia instance
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue);

        // Initialize theme store after mounting
        app.mount(el);

        // Initialize theme from localStorage
        const themeStore = useThemeStore();
        themeStore.init();

        return app;
    },
    progress: {
        color: '#6366f1',
    },
});
