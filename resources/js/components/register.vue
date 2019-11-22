<template>
  <div>
    <!-- US2
        As an anonymous user I want to create an account with an associated virtual wallet by
        registering myself on the platform. Registration data should include user’s name (only
        spaces and letters), e-mail (must be unique), password (3 or more characters), NIF (fiscal
        identification) and an optional photo (by uploading an image). When the user's account is
        created it will be automatically associated with a virtual wallet with the balance value of
        zero. 
    -->
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div>
        <label for="inputName">Name</label>
        <input
            type="text"
            class="form-control"
            v-model="registerName"
            name="name"
            id="inputName"
            placeholder= "Firstname Lastname"
        />
        <label for="inputEmail">Email</label>
        <input
            type="email"
            class="form-control"
            v-model="registerEmail"
            name="email"
            id="inputEmail"
            placeholder= "user1@mail.pt"
        />
        <label for="inputPassword">Password</label>
        <input
            type="password"
            class="form-control"
            v-model="registerPassword"
            name="password"
            id="inputPassword"
        />
        <label for="inputNif">NIF</label>
        <input
            type="number"
            class="form-control"
            v-model="registerNif"
            name="nif"
            id="inputNif"
            min="100000000"
            max="999999999"
        />
      <a class="btn btn-primary" v-on:click.prevent="tryRegister()">Register</a>
    </div>
    <div class="alert alert-warning" v-if="showWarning">
      <button type="button" class="close-btn" v-on:click="showWarning=false">&times;</button>
      <strong>{{ warningMessage }}</strong>
    </div>
  </div>
</template>

<script>

export default {
    data: () => {
        return {
            title : "Register",
            showWarning : false,
            warningMessage : "",
            registerName: "",
            registerEmail: "",
            registerPassword: "",
            registerNif : 0,
            user: {
                name : "",
                email: "",
                password: "",
                nif: null
            },
        };
    },
    methods: {
        tryRegister : function(){
            //TODO: Create wallet on register and assign it to user (?)
            this.showWarning = false;
            this.warningMessage = "";
            this.user.name = this.registerName;
            this.user.email = this.registerEmail;
            this.user.password = this.registerPassword; //Bcrypt this (HOW?)
            this.user.nif = this.registerNif;
            var emailValid = this.validEmail(this.user.email);
            var nameValid = this.validName(this.user.name);
            var passwordValid = this.validPassword(this.user.password);
            var nifValid = this.validNif(this.user.nif);
            axios.get("api/users/emailavailable",{params: { email: this.user.email }}).then(response => {
                if(response.data === true){
                    if(!nameValid){
                        this.showWarning = true;
                        this.warningMessage += "Name can only be spaces and letters,";
                    }
                    if(!emailValid){
                        this.showWarning = true;
                        this.warningMessage += "Email invalid,";
                    }
                    if(!passwordValid){
                        this.showWarning = true;
                        this.warningMessage += "Password must have 3 or more characters,";
                    }
                    if(!nifValid){
                        this.warningMessage += "Nif must be a 9 digit number,";
                    }
                    if(nameValid && emailValid && passwordValid && nifValid){
                        axios.post("api/users",{
                            name: this.user.name,
                            email: this.user.email,
                            password : this.user.password,
                            nif : this.user.nif
                        }).then(() => {
                            this.$router.push({ path: '/login' });
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                }else{
                    this.showWarning = true;
                    this.warningMessage += "Email already in use,";
                }
            });
        },
        validName: function(name){
            if(!name){
                return false;
            }
            var re = /^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/;
            return re.test(name) && name.length >= 3;
        },
        validEmail: function (email) {
            if(!email){
                return false;
            }
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        validPassword: function(password){
            if(!password){
                return false;
            }
            return(password.length >= 3);
        },
        validNif: function(nif){
            if(!nif){
                return false;
            }
            return(nif >= 100000000 && nif <= 999999999);
        }
    },
    mounted() {
        
    }
};
</script>

<style>
</style>