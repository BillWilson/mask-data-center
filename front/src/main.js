import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import router from './router'
import { Icon }  from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'
import VueAxios from 'vue-axios'
const moment = require('moment');
require('moment/locale/zh-tw');

Vue.use(VueAxios, axios);
Vue.use(require('vue-moment'), {
    moment
});
// this part resolve an issue where the markers would not appear
delete Icon.Default.prototype._getIconUrl;

Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

Vue.config.productionTip = false;

new Vue({
  vuetify,
  router,
  render: h => h(App)
}).$mount('#app');
