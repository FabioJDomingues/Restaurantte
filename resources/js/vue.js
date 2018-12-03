/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Importar o vue-router
 * � necess�rio instalar o vue-router primeiro (npm install vue-router --save)
 */
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import store from './stores/global-store';
Vue.use(store);

const users = Vue.component('users-component', require('./components/users.vue'));
// @ Unused
//const userList = Vue.component('list-users', require('./components/userList.vue'));
const managerList = Vue.component('list-managers', require('./components/managerList.vue'));
const cookList = Vue.component('list-cooks', require('./components/cookList.vue'));
const waiterList = Vue.component('list-waiters', require('./components/waiterList.vue'));
const cashierList = Vue.component('list-cashiers', require('./components/cashierList.vue'));

const menu = Vue.component('items-component', require('./components/menu.vue'));
// @ Unused
//const menuList = Vue.component('list-menu', require('./components/menuList.vue'));
const dishList = Vue.component('list-dishes', require('./components/dishList.vue'));
const drinksList = Vue.component('list-drinks', require('./components/drinkList.vue'));

const login = Vue.component('login-component', require('./components/login.vue'));
const logout = Vue.component('logout-component', require('./components/logout.vue'));

const routes = [
    { path: '/', redirect: '/menu' },
    { path: '/users', component: users },
    { path: '/menu', component: menu },
    { path: '/login', component: login },
    { path: '/logout', component: logout }
];

const router = new VueRouter({
    routes
});

const app = new Vue({
    // Elemento que o vue vai poder "gerir"
    el: '#app',
    router,
    data: {
        userToken: undefined,
        isUserAuthenticated: false
    },
    store
});
