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
        <div class="file-upload-form">
            Upload an image file:
            <input type="file" @change="previewImage" accept="image/*">
            <div>
                <img 
                    class="preview" 
                    v-if="registerPhotoImage" 
                    :src="registerPhotoImage"
                    height="256px"
                    width="256px"
                    accept="image/*"
                >
            </div>
        </div>
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
            registerPhotoImage : null,
            registerPhotoFile : null,
            user: {
                name : "",
                email: "",
                password: "",
                nif: null,
                photo: null,
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
            this.user.photo = this.registerPhotoFile;
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
                        let formData = new FormData();
                        formData.append('photo', this.registerPhotoFile);
                        formData.set('name',this.user.name);
                        formData.set('email',this.user.email);
                        formData.set('password',this.user.password);
                        formData.set('nif',this.user.nif);
                        axios.post("api/users",formData).then(() => {
                            this.$router.push({ path: '/login' });
                        }).catch(function (error) {
                            console.log(error);
                        });
                        /*
                        let formData = new FormData();
                        formData.append('photoFile', this.registerPhotoFile);
                        axios.post("api/users",formData,{
                            name: this.user.name,
                            email: this.user.email,
                            password : this.user.password,
                            nif : this.user.nif,
                            photo: this.user.photo
                        }).then(() => {
                            this.$router.push({ path: '/login' });
                        }).catch(function (error) {
                            console.log(error);
                        });
                        */
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
        },
        previewImage: function(event) {
            // Reference to the DOM input element
            var input = event.target;
            // Ensure that you have a file before attempting to read it
            if (input.files && input.files[0]) {
                // create a new FileReader to read this image and convert to base64 format
                var reader = new FileReader();
                // Define a callback function to run, when FileReader finishes its job
                reader.onload = (e) => {
                    // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
                    // Read image as base64 and set to imageData
                    console.log(e.target.result);
                    this.registerPhotoImage = e.target.result;
                }
                // Start the reader job - read file as a data url (base64 format)
                reader.readAsDataURL(input.files[0]);
                this.registerPhotoFile = input.files[0];
            }
        }
    },
    mounted() {
        
    }
};
</script>

<style>
</style>