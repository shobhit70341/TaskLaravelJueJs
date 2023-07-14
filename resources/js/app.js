/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import 'bootstrap/dist/css/bootstrap.css';
import { createApp } from "vue";

import TaskManager from './components/TaskManager.vue';

createApp(TaskManager).mount("#app");
