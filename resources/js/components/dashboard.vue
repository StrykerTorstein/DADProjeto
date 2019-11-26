<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
      <div>
        <v-img
          v-if="user"
          height="256px"
          width="256px"
          :src="getPhoto()"
        >
        </v-img>
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
        },
        getPhoto(){
          return "storage/fotos/" + this.$data.user.photo;
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