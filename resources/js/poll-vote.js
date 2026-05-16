import './bootstrap';
import { createApp } from 'vue';
import App from './AppPollVote.vue';

const el = document.getElementById('poll-vote-app');
const poll = JSON.parse(el.dataset.poll ?? '{}');
const isAuthenticated = JSON.parse(el.dataset.isAuthenticated ?? 'false');
const loginUrl = el.dataset.loginUrl ?? '/login';

createApp(App, { poll, isAuthenticated, loginUrl }).mount(el);
