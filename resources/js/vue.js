/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'
import Vuetify from "vuetify";
import Vuex from 'vuex'

window.Vue = require('vue');

Vue.use(Vuex)
import store from './store/auth';

Vue.use(Vuetify)
import "vuetify/dist/vuetify.min.css";

import VueRouter from 'vue-router';
import Welcome from './components/welcome'
import Users from './components/users';
import TicTacToe from './components/game';
import Login from './components/login';
import Dashboard from './components/dashboard';
import Register from './components/register';

Vue.use(VueRouter);
//Vue Router makes the line below deprecated
//Vue.component('users',Users);

const routes = [
    {path:'/',redirect:'/welcome'},
    {path:'/welcome',component:Welcome},
    {path:'/login',component:Login},
    {path:'/register',component:Register},
    {path:'/dashboard', component:Dashboard},
    {path:'/users',component:Users},
    {path:'/game',component:TicTacToe}
]

const router = new VueRouter({
    routes:routes
});

const app = new Vue({
    el: '#app',
    store,
    router,
    data: {
        //departments: [],
        player1: undefined,
        player2: undefined
    },
    methods: {
        
    },
    mounted() {
        /*
        axios.get("api/departments").then(response => {
          this.departments = response.data.data;
        });
        */
    }
});
