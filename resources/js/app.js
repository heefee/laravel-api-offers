import './bootstrap';
import { createApp } from 'vue';
import AuthenticationForm from './components/AuthenticationForm.vue';

const app = createApp({});

app.component('AuthenticationForm', AuthenticationForm);

app.mount('#app');
