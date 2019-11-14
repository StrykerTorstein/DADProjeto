/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Users from './components/users';
import Departments from './components/departments'
import TicTacToe from './components/game'
import VueRouter from 'vue-router';

Vue.use(VueRouter);
//Vue Router makes the line below deprecated
//Vue.component('users',Users);

const routes = [
    {path:'/',component:Users},
    {path:'/departments',component:Departments},
    {path: '/game',component:TicTacToe}
]

const router = new VueRouter({
    routes:routes
});

const app = new Vue({
    el: '#app',
    router,
    data: {
        departments: [],
        player1: undefined,
        player2: undefined
    },
    methods: {
        
    },
    mounted() {
        axios.get("api/departments").then(response => {
          this.departments = response.data.data;
        });
    }
});