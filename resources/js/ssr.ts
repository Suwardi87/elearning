import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const appName = import.meta.env.VITE_APP_NAME || 'E-Learning';

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => (title ? `${title} - ${appName}` : appName),
            resolve: resolvePage,
            setup: ({ App, props, plugin }) => createSSRApp({ render: () => h(App, props) }).use(plugin),
        }),
    { cluster: true },
);

function resolvePage(name: string) {
    const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');

    return resolvePageComponent<DefineComponent>(`./Pages/${name}.vue`, pages);
}
