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
import Vuelidate from 'vuelidate'
import VueProgressBar from 'vue-progressbar'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';
 

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
Vue.use(Vuelidate)
Vue.use(VueSweetalert2);
Vue.component('VueCtkDateTimePicker', VueCtkDateTimePicker);

Vue.use(new VueSocketIO({
  debug: false,
  connection: 'http://127.0.0.1:3000',
}))

Vue.use(VueProgressBar, {
  color: 'rgb(0, 128, 255)',
  failedColor: 'red',
  height: '10px'
})

new Vue({
  vuetify,
  router,
  render: h => h(App)
}).$mount('#app')
