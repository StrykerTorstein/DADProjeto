<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <a class="btn btn-primary" v-on:click.prevent="debug()">Debug</a>
  </div>
</template>

<script>

export default {
    data: () => {
        return {
            title : "Welcome",
            user : null
        };
    },
    methods: {
        debug: function(){
            console.log(this.$data.user);
        }
    },
    mounted() {
        this.$store.commit("loadTokenAndUserFromSession");
        this.$data.user = this.$store.state.user;
    }
};
</script>

<style>
</style>