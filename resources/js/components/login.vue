<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div class="jumbotron">
      <div class="form-group">
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
      <div>
      <label for="inputPassword">Password</label>
      <div>
      <input
        type="password"
        class="form-control"
        v-model="loginPassword"
        name="password"
        id="inputPassword"
      />
      </div>
      </div>
      <div>
      <v-btn color="primary" id="btn" v-on:click.prevent="login(loginEmail,loginPassword)">Login</v-btn>
      <v-btn color="primary" id="btn" v-on:click.prevent="gotoRegisterPage()">Register</v-btn>
      </div>
    </div>
    <div class="alert alert-warning" v-if="showWarning">
      <button type="button" class="close-btn" v-on:click="showWarning=false">&times;</button>
      <strong>{{ warningMessage }}</strong>
    </div>
    </div>
  </div>
</template>

<script>

export default {
  data: () => {
    return {
      title: "Login Menu",
      loginEmail: "user1@mail.pt",
      loginPassword: "123",
      user: {
        email: "",
        password: "",
        user: undefined
      },
      number: 0,
      showWarning: false,
      warningMessage : ""
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
        axios.get("api/user").then(response => {
          //Todo: Implement socket
          //this.$socket.emit('login',response.data.data);

          if(response.data.active === 0){
            this.$toasted.show("Youre not active on this platform any longer. Contact an admin.",{type: "error"});
            return;
          }
          this.$store.commit("setUser",response.data);
          
          if(response.data){
            this.$socket.emit('login',response.data);
          }

          this.$router.push({ path: '/dashboard' });
        });
      }).catch(error => { 
        this.showWarning = true;
        this.warningMessage = "Login failed! Invalid credentials.";
      }  
    )},
    gotoRegisterPage(){
      this.$router.push({ path: '/register' });
    }
  },
  mounted() {
    
  },

};
</script>

<style>
  #btn{
    color:black;
  }
</style>