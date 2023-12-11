import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"

import '@mdi/font/css/materialdesignicons.css'

import './assets/css/style.css'

const vuetify = createVuetify({
    components,
    directives
  })

const options = {
  shareAppContext: true,
  draggable: false,
  closeOnClick: false,
  pauseOnHover: false,
  icon: false,
  toastClassName: ['rounded-0'],
  containerClassName: 'custom-toast'

}

createApp(App).use(router).use(vuetify).use(Toast, options).mount('#app')
