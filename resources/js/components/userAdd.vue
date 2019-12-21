<template>
    <div>
        <div class="jumbotron">
            <h1>{{ title }}</h1>
        
        <div>
            <label for="inputName">Name</label>
            <input
                type="text"
                class="form-control"
                v-model="addName"
                name="name"
                id="inputName"
            />
            <br>
            <label for="inputType">User type</label>
            <select
              class="form-control"
              v-model="addType"
              name="type"
            >
              <option v-for="(item, key) in userTypes" v-bind:key="key" :value="key">{{item}}</option>
            </select>
            <br>
            <label for="inputEmail">Email</label>
            <input
                type="email"
                class="form-control"
                v-model="addEmail"
                name="email"
                id="inputEmail"
            />
            <br>
            <label for="inputPassword">Password</label>
            <input
                type="password"
                class="form-control"
                v-model="addPassword"
                name="password"
                id="inputPassword"
            />
            <br>
            <div class="file-upload-form">
                Upload an image file:
                <input type="file" @change="previewImage" accept="image/*">
                <div>
                    <img 
                        class="preview" 
                        v-if="addPhotoImage" 
                        :src="addPhotoImage"
                        height="256px"
                        width="256px"
                        accept="image/*"
                    >
                </div>
            </div>
            <br>
            <a class="btn btn-primary" v-on:click.prevent="addUser()">Add</a>
            <a class="btn btn-danger" v-on:click.prevent="cancelAdd()">Cancel</a>
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
      title: "Add User",
      showWarning : false,
      warningMessage : "",
      userTypes: {
        a: "Administrator",
        o: "Operator",
      },
      addName: "",
      addEmail: "",
      addPassword: "",
      addType : "",
      addPhotoImage : null,
      addPhotoFile : null,
      user: {
          name : "",
          email: "",
          password: "",
          type: " ",
          photo: null,
      },
    };
  },
  methods: {
    addUser : function(){
            //TODO: Create wallet on register and assign it to user (?)
            this.showWarning = false;
            this.warningMessage = "";
            this.user.name = this.addName;
            this.user.email = this.addEmail;
            this.user.password = this.addPassword; //Bcrypt this (HOW?)
            this.user.type = this.addType;
            this.user.photo = this.addPhotoFile;
            var emailValid = this.validEmail(this.user.email);
            var nameValid = this.validName(this.user.name);
            var passwordValid = this.validPassword(this.user.password);
            var typeValid = this.validType(this.user.type);
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
                    if(!typeValid){
                        this.warningMessage += "Type must be admin or operator";
                    }
                    if(nameValid && emailValid && passwordValid && typeValid){
                        let formData = new FormData();
                        formData.append('photo', this.addPhotoFile);
                        formData.set('name',this.user.name);
                        formData.set('email',this.user.email);
                        formData.set('password',this.user.password);
                        formData.set('type',this.user.type);
                        axios.post("api/users/add",formData).then(() => {
                            this.$router.push({ path: '/users' });
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
        validType: function(type){
            if(!type){
                return false;
            }
            return(type == "a" || type == "o");
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
                    this.addPhotoImage = e.target.result;
                }
                // Start the reader job - read file as a data url (base64 format)
                reader.readAsDataURL(input.files[0]);
                this.addPhotoFile = input.files[0];
            }
        },
    cancelAdd() {
      this.$router.push({ path: '/users' });
    },
  },mounted(){
    //Para mostrar que é possivel aceder a variaveis no root (vue.js file)
    //this.departments = this.$root.departments;
  }
};
</script>