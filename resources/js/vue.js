/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'
import Vuetify from 'vuetify';
import Vuex from 'vuex'
import store from './store/auth';
import "vuetify/dist/vuetify.min.css";
import vuetify from './plugins/vuetify';
Vue.use(Vuetify);

window.Vue = require('vue');

Vue.use(Vuex);


import VueRouter from 'vue-router';
import Welcome from './components/welcome'
import Users from './components/users';
import TicTacToe from './components/game';
import Dashboard from './components/dashboard';
import Register from './components/register';
import Movement from './components/movement';
import Chat from './components/chat';

import LoginComponent from "./components/login.vue";
import LogoutComponent from "./components/logout.vue";

const Login = Vue.component("login", LoginComponent);
const Logout = Vue.component("logout", LogoutComponent);

import VueSocketIO from 'vue-socket.io';
//Replace the connection with the IP of the server being used
Vue.use(new VueSocketIO(
    {
        debug: true,
        connection: 'http://localhost:8080'
    }));


Vue.use(VueRouter);
//Vue Router makes the line below deprecated
//Vue.component('users',Users);

import Toasted from "vue-toasted";
Vue.use(Toasted, {
    position: "bottom-center",
    duration: 5000,
    type: "info"
});

const routes = [
    { path: '/', redirect: '/welcome' },
    { path: '/welcome', component: Welcome, name: "welcome" },
    { path: '/register', component: Register, name: "register" },
    { path: '/dashboard', component: Dashboard, name: "dashboard" },
    { path: '/users', component: Users },
    { path: '/game', component: TicTacToe },
    { path: '/movements', component: Movement, name: "movements" },
    { path: '/chat', component: Chat, name: "chat" },
    { path: '/login', component: Login, name: "login" },
    { path: '/logout', component: Logout, name: "logout" }
]

const router = new VueRouter({
    routes: routes
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
            vuetify
            next("/login");
            return;
        }
    }
    next();
});

const app = new Vue({
    store,
    router,
    vuetify,
    data: {

    },
    created() {
        this.$store.commit("loadTokenAndUserFromSession");
        if (this.$store.state.user) {
            this.$socket.emit('login', this.$store.state.user);
        }
    },
    methods: {

    },
    mounted() {

    },
    sockets: {
        //globally used socket methods
        /*
        movementSent (dataFromServer) {
            console.log("movement sent!");
            //this.$toasted.success('Message "' + dataFromServer[0] + '" was sent to "' +
            //dataFromServer[1].name + '"');
        }
        */
        movementReceived(dataFromServer) {
            console.log(dataFromServer);
            this.$toasted.success(`New Movement`);
        }
    },
}).$mount("#app");
