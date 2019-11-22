<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
      <h2>Number of virtual wallets our users have: {{number}}</h2>
    </div>
    <div>
      <a class="btn btn-primary" v-on:click.prevent="gotoLoginPage()">Login</a>
      <a class="btn btn-secondary" v-on:click.prevent="gotoRegisterPage()">Register</a>
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
</style>