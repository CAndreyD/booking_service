import { createApp, h } from 'vue'
// import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { createInertiaApp } from '@inertiajs/vue3'

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { InertiaProgress } from '@inertiajs/progress'
// import { route } from 'ziggy-js'
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'

InertiaProgress.init()

createInertiaApp({
  title: (title) => `${title ? title + ' - ' : ''}MyApp`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue) // если используешь ziggy
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
  historyEncryption: true, // ✅ включаешь шифрование истории
})
