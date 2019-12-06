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

Vue.use(Vuex);
import store from './store/auth';

Vue.use(Vuetify);
import "vuetify/dist/vuetify.min.css";

import VueRouter from 'vue-router';
import Welcome from './components/welcome'
import Users from './components/users';
import TicTacToe from './components/game';
//import Login from './components/login';
import Dashboard from './components/dashboard';
import Register from './components/register';
import Movement from './components/movement';
import LoginComponent from "./components/login.vue";
import LogoutComponent from "./components/logout.vue";


const Login = Vue.component("login", LoginComponent);
const Logout = Vue.component("logout", LogoutComponent);


Vue.use(VueRouter);
//Vue Router makes the line below deprecated
//Vue.component('users',Users);

const routes = [
    {path:'/',redirect:'/welcome'},
    {path:'/welcome',component:Welcome, name: "welcome"},
    {path:'/register',component:Register, name: "register"},
    {path:'/dashboard', component:Dashboard, name: "dashboard"},
    {path:'/users',component:Users},
    {path:'/game',component:TicTacToe},
    {path:'/movements',component:Movement, name: "movements"},
    {path:'/login', component: Login, name: "login"},
    {path:'/logout', component: Logout, name: "logout"}
]

const router = new VueRouter({
    routes:routes
});

router.beforeEach((to, from, next) => {
    if (to.name == "welcome") {
        if (store.state.user) {
            next("/dashboard");
            return;
        }
    }
    if (to.name == "logout") {
        if (!store.state.user) {
            next("/login");
            return;
        }
    }
    next();
});

const app = new Vue({
    store,
    router,
    data: {
        //departments: [],
        player1: undefined,
        player2: undefined
    },
    created() {
        this.$store.commit("loadTokenAndUserFromSession");
        /*
        if(this.$store.state.user){
            this.$socket.emit('login',this.$store.state.user);
        }
        */
    },
    methods: {
        
    },
    mounted() {
        
    }
}).$mount("#app");
