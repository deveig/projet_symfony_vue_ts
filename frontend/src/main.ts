import './assets/css/normalize.css';
import './assets/css/main.css';
import { type App } from 'vue';
// import RecipePage from './components/RecipePage.vue';

// const app = createApp(App);
// app.mount('#app');
import { createSSRApp } from 'vue'
import RecipeApp from './App.vue';

// SSR requires a fresh app instance per request, therefore we export a function
// that creates a fresh app instance. If using Vuex, we'd also be creating a
// fresh store here.
export function createApp() : {app: App<Element>} {
  const app = createSSRApp(RecipeApp)
  return { app }
}