import { createApp } from "vue";
import { createPinia } from "pinia";
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import {useDashboardStore} from "@/stores/dashboard";

import App from "./App.vue";
import router from "./router";

import "./assets/main.css";

const vuetify = createVuetify({
    components,
    directives,
})


const app = createApp(App);
app.use(createPinia());
app.use(vuetify)

const dashboardStore = useDashboardStore()
Promise.all([
   dashboardStore.fetchDashboard()
]).then(() => {
    app.use(router)
    app.mount('#app')
})

