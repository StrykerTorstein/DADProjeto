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
            title : "Dashboard",
            user: null
        };
    },
    methods: {
        debug: function(){
          console.log(this.user);
        },
        getPhoto(){
          return "storage/fotos/" + this.user.photo;
        }
    },
    mounted() {
        if(this.$store.state.user){
          this.user = this.$store.state.user;
          this.title = `'${this.user.name}' Dashboard`;
        }else{
          console.log("Not logged in, cannot access the dashboard page!");
          this.$router.push({ path: '/welcome' });
        }
        //this.$store.commit("loadTokenAndUserFromSession");
        //this.$data.user = this.$store.state.user;
    }
};
</script>

<style>
</style>