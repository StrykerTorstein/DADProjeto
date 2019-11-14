@extends('master')

@section('title', 'Vue.js App')

@section('content')
    <!--<users></users>-->
    <router-link to="/">Users</router-link>
    <router-link to="/departments">Departments</router-link>
    <router-link to="/game">Tic Tac Toe</router-link>
    <router-view></router-view>
@endsection

@section('pagescript')
    <script src="js/vue.js"></script>
@stop  
