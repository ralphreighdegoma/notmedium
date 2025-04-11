import { createApp } from 'vue';
import { Quasar } from 'quasar';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import '@quasar/extras/material-icons/material-icons.css';
import 'quasar/src/css/index.sass';
import { useAuthStore } from './stores/auth';
import axios from 'axios';

axios.defaults.baseURL = '/';

const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
const pinia = createPinia();

app.use(Quasar, {
  plugins: {},
});
app.use(pinia);
app.use(router);

const authStore = useAuthStore();

app.mount('#app');