<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
      <h2>Number of virtual wallets: {{number}}</h2>
    </div>
    <div>
      <v-btn color="primary" id="btn" v-show="!this.$store.state.user" v-on:click.prevent="gotoLoginPage()">Login</v-btn>
      <v-btn color="primary" id="btn" v-show="!this.$store.state.user" v-on:click.prevent="gotoRegisterPage()">Register</v-btn>
    </div>
  </div>
</template>

<script>

export default {
  data: () => {
    return {
      title: "Virtual Wallet",
      number: 0
    };
  },
  methods: {
    getNumberOfWallets: function() {
      axios.get("api/wallets/count").then(response => {
        this.number = response.data.total;
      });
    },
    gotoLoginPage: function(){
      this.$router.push({ path: '/login' });
    },
    gotoRegisterPage: function(){
      this.$router.push({ path: '/register' });
    }
  },

  mounted() {
    this.getNumberOfWallets();
  },

};
</script>

<style>
  #btn{
    color:black;
  }
</style>