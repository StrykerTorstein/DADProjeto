<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
  </div>
</template>

<script>

export default {
  data: () => {
    return {
      title: "Platform Statistics"
    };
  },
  methods: {
      
  },
  mounted() {
      if(this.$store.state.user && this.$store.state.user.type != 'a'){
        this.$router.push({ path: '/welcome' });
        return;
      }
  },
};
</script>

<style>
  #btn{
    color:black;
  }
</style>