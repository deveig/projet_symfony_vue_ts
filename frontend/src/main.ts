import './assets/css/normalize.css';
import './assets/css/main.css';

import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router';
import { createApp } from 'vue';
import App from './App.vue';
import RecipePage from './components/RecipePage.vue';

const route = [{ path: '/recipe', component: RecipePage }];
const router = createRouter({ history: createWebHistory(), routes: route });

const app = createApp(App);
app.use(router);
app.mount('#app');
