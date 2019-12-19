@extends('master')

@section('title', 'Vue.js App')

@section('content')
    <!--<users></users>-->
    <router-link to="/">Welcome</router-link>
    <router-link v-show="!this.$store.state.user" to="/login">Login</router-link>
    <router-link v-show="this.$store.state.user" to="/dashboard">Dashboard</router-link>
    <router-link v-show="this.$store.state.user" to="/movements">Movements</router-link>
    <router-link v-show="this.$store.state.user && this.$store.state.user.type == 'u'" to="/movementStatistics">Statistics</router-link>
    <router-link v-show="this.$store.state.user && this.$store.state.user.type == 'a'" to="/adminStatistics">Analytics</router-link>
    <!--<router-link to="/game">Tic Tac Toe</router-link>-->
    <router-link to="/chat">Chat</router-link>
    <router-link v-show="this.$store.state.user" to="/logout">Logout</router-link>
    <router-view></router-view>
@endsection

@section('pagescript')
    <script src="js/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
    <script src="https://unpkg.com/vuex@3.1.2/dist/vuex.js"></script>
    
@stop  
