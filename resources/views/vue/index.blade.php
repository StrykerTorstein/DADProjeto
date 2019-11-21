@extends('master')

@section('title', 'Vue.js App')

@section('content')
    <!--<users></users>-->
    <router-link to="/">Login</router-link>
    <!--<router-link to="/departments">Departments</router-link>-->
    <router-link to="/game">Tic Tac Toe</router-link>
    <router-view></router-view>
@endsection

@section('pagescript')
    <script src="js/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
    <script src="https://unpkg.com/vuex@3.1.2/dist/vuex.js"></script>
@stop  
