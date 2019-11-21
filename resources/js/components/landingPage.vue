<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
      <h1>{{number}}</h1>
    </div>
    <div class="jumbotron"><div class="form-group">
      <h2>Login Credentials</h2>
      <label for="inputEmail">Email</label>
      <input
        type="text"
        class="form-control"
        v-model="loginEmail"
        name="email"
        id="inputEmail"
        placeholder= "user1@mail.pt"
      />
      <label for="inputPassword">Password</label>
      <input
        type="password"
        class="form-control"
        v-model="loginPassword"
        name="password"
        id="inputPassword"
      />
      <a class="btn btn-primary" v-on:click.prevent="login(loginEmail,loginPassword)">Login</a>
<!--
      <label for="MyStoreTest">Store:</label>
      <input
        type="text"
        class="form-control"
        v-model="storeTestInput"
        name="Test"
        id="MyStoreTest"
        value = storetest
      /> -->
    </div>
    </div>
  </div>
</template>

<script>

export default {
  data: () => {
    return {
      title: "Virtual Wallet",
      loginEmail: "user1@mail.pt",
      loginPassword: "123",
      user: {
        email: "",
        password: "",
        user: undefined
      },
      number: 0
    };
  },
  methods: {
    login: function(email,password){
      this.user.email = email;
      this.user.password = password;
      this.user.user = this.$store.state.user;
      axios.post("api/login", this.user).then(response => {
        var token = response.data.access_token;
        this.$store.commit("setToken",token);
        console.log("User logged in!");
        this.$router.push('/userPage');
        axios.get("api/user").then(response => {
            //console.log(response.data);
            this.$store.commit("setUser",response.data);
        });

      }).catch(error => { console.log ("Cannot login!"); })
    },
    getNumberOfWallets: function() {
      axios.get("api/wallets/count").then(response => {
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