import { createApp } from './main';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';

const route = [{ path: '/', component: App }];
const router = createRouter({ history: createWebHistory(), routes: route });

const { app } = createApp()
app.use(router);
app.mount('#app')