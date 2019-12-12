<template>
  <div class="jumbotron">
    <v-btn class="btn btn-primary" v-on:click.prevent="editUser = true">Edit Profile</v-btn>
    <div v-if="editUser">
      <v-btn class="btn btn-success" v-on:click.prevent="saveUserEdits()">Save</v-btn>
      <v-btn class="btn btn-danger" v-on:click.prevent="editUser = false">Cancel</v-btn>
    </div>
  </div>
</template>

<script>
import { request } from "http";
export default {
  data: () => {
    return {
      editUser: false,  
      photo: null
    };
  },
  methods: {
    saveUserEdits(){
        
    }
  },
  mounted() {
    if (this.$store.state.user) {
      this.title = `'${this.$store.state.user.name}' Dashboard`;
      if (
        this.$store.state.user.photo !== "null" &&
        this.$store.state.user.photo
      ) {
        this.photo = "storage/fotos/" + this.$store.state.user.photo;
      }
    } else {
      console.log("Not logged in, cannot access the dashboard page!");
      this.$router.push({ path: "/welcome" });
    }
  }
};
</script>

<style>
</style>