import Vue from 'vue';
import App from './App.vue';
import vuetify from './plugins/vuetify';
import VueRouter from 'vue-router';
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueApexCharts from 'vue-apexcharts'
import Notifications from 'vue-notification'
import moment from 'moment-timezone'
import VueSocketIO from 'vue-socket.io'
import VueKonva from 'vue-konva'

moment.tz.setDefault('Asia/Jakarta')

// router setup
import routes from "./routes/routes";

// configure router
const router = new VueRouter({
  routes, // short for routes: routes
  linkExactActiveClass: "nav-item active"
});

Vue.config.productionTip = false
Vue.use(VueKonva)
Vue.use(VueRouter);
Vue.use(VueAxios, axios)
Vue.use(VueApexCharts)
Vue.use(Notifications)

Vue.use(new VueSocketIO({
  debug: false,
  connection: 'http://localhost:3000',
}))


new Vue({
  vuetify,
  router,
  render: h => h(App)
}).$mount('#app')
