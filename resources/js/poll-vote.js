import './bootstrap';
import { createApp } from 'vue';
import App from './AppPollVote.vue';

const el = document.getElementById('poll-vote-app');
const poll = JSON.parse(el.dataset.poll ?? '{}');

createApp(App, { poll }).mount(el);
