<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
      <h1>{{number}}</h1>
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
      axios.get("api/wallets").then(response => {
        this.number = response.data.total;
      });
    }
  },

  mounted() {
    this.getNumberOfWallets();
  },

};
</script>

<style>
</style>