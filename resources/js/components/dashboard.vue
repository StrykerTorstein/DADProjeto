<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div>
      <v-img v-if="photo" height="256px" width="256px" :src="photo"></v-img>
    </div>
    <div v-if="this.$store.state.user.type == 'o'">
      <!-- place operator component here -->
      <operator-movement />
    </div>
    <user-edit />
    <!--<v-btn class="btn btn-primary" v-on:click.prevent="debug()">Debug</v-btn>-->
  </div>
</template>

<script>
import operatorMovement from "./operatorMovement";
import userEdit from "./userEditProfile";
import { request } from "http";
export default {
  data: () => {
    return {};
  },
  methods: {
    debug: function() {
      //Put this in a creation of a payment
    }
  },
  mounted() {
    if (!this.$store.state.user) {
      console.log("Not logged in, cannot access the dashboard page!");
      this.$toasted.show("Not logged in, cannot access the dashboard page!", {
        type: "error"
      });
      this.$router.push({ path: "/welcome" });
    }
  },
  components: {
    "operator-movement": operatorMovement,
    "user-edit": userEdit
  },
  computed: {
    title() {
      return `'${this.$store.state.user.name}' Dashboard`;
    },
    photo() {
      if(this.$store.state.user.photo){
        return "storage/fotos/" + this.$store.state.user.photo;
      }
      return null;
    }
  }
};
</script>

<style>
</style>