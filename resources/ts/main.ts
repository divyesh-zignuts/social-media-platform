/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import layoutsPlugin from '@/plugins/layouts'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import '@core-scss/template/index.scss'
import '@styles/styles.scss'
import { createHead } from '@unhead/vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { createPinia } from 'pinia'
import { createApp } from 'vue'

loadFonts()

// Create vue app
const app = createApp(App)
const head = createHead()

// define options for QuillEditor
const globalOptions = {
  placeholder: 'Add Bio',
}

// set default globalOptions prop
QuillEditor.props.globalOptions.default = () => globalOptions

// Use plugins
app.use(vuetify)
app.component('QuillEditor', QuillEditor)
app.use(head)
app.use(createPinia())
app.use(router)
app.use(layoutsPlugin)

// Mount vue app
app.mount('#app')
