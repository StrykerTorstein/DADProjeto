<template>
  <div class="jumbotron">
    <v-btn class="btn btn-primary" v-on:click.prevent="editUser = true">Edit Profile</v-btn>
    <div v-if="editUser">
      <div>
        <div>
        Update Name
        <input type="checkbox" v-model="updateName">
        </div>
        <div v-if="updateName">
        <label for="inputName">Name</label>
        <input
          type="text"
          class="form-control"
          v-model="newName"
          name="name"
          id="inputName"
          placeholder="Firstname Lastname"
        />
        </div>
        <div>
        Update Password
        <input type="checkbox" v-model="updatePassword">
        </div>
        <div v-if="updatePassword">
        <label for="inputPassword">Password</label>
        <input
          type="password"
          class="form-control"
          v-model="newPassword"
          name="password"
          id="inputPassword"
        />
        <label for="inputPassword">Type Password Again</label>
        <input
          type="password"
          class="form-control"
          v-model="newPasswordCheck"
          name="passwordCheck"
          id="inputPasswordCheck"
        />
        </div>
        <div v-if="this.$store.state.user.type == 'u'">
        <div>
        Update NIF
        <input type="checkbox" v-model="updateNif">
        </div>
          <div v-if="updateNif">
          <label for="inputNif">NIF</label>
          <input
            type="number"
            class="form-control"
            v-model="newNif"
            name="nif"
            id="inputNif"
            min="100000000"
            max="999999999"
          />
          </div>
        </div>
        <div>
        Update Photo
        <input type="checkbox" v-model="updatePhoto">
        </div>
        <div class="file-upload-form" v-if="updatePhoto">
          Upload an image file:
          <input type="file" @change="previewImage" accept="image/*" />
          <div>
            <img
              class="preview"
              v-if="newPhotoImage"
              :src="newPhotoImage"
              height="256px"
              width="256px"
              accept="image/*"
            />
          </div>
        </div>
      </div>
      <v-btn v-if="updateAnything" class="btn btn-success" v-on:click.prevent="saveUserEdits()">Save and Logout</v-btn>
      <v-btn class="btn btn-danger" v-on:click.prevent="editUser = false">Cancel</v-btn>
    </div>
  </div>
</template>

<script>
import { request } from "http";
export default {
  data: () => {
    return {
      editUser: false,
      photo: null,
      newName: null,
      newPassword: null,
      newPasswordCheck: null,
      showAlertPasswordMustBeDifferent: false,
      newPhotoImage: null,
      newPhotoFile: null,
      newNif: null,
      updateName: false,
      updatePassword: false,
      updateNif: false,
      updatePhoto: false
    };
  },
  watch: {
    editUser: function(n) {
      if (n === true) {
        this.newName = this.$store.state.user.name;
        this.newNif = this.$store.state.user.nif;
      }
    }
  },
  methods: {
    saveUserEdits() {
      if(this.updatePassword){
        if (!this.newPassword || this.newPassword.length < 3) {
          this.$toasted.show("Password must be larger than 3 characters!", {
            type: "error"
          });
          return;
        }
        if (this.newPassword.localeCompare(this.newPasswordCheck) != 0) {
          this.$toasted.show("Please type the correct password twice!", {
            type: "error"
          });
          return;
        }
      }
      if(this.updateName){
        var nameValid = this.validateName(this.newName);
        if (!nameValid) {
          this.$toasted.show("Invalid Name", {
            type: "error"
          });
          return;
        }
      }
      if (this.$store.state.user.type == "u" && this.updateNif) {
        var nifvalid =
          this.newNif !== null &&
          (this.newNif >= 100000000 && this.newNif <= 999999999);
        if (!nifvalid) {
          this.$toasted.show("Invalid NIF (must be 9 digits long)", {
            type: "error"
          });
          return;
        }
      }
      axios
        .get("api/users/checkNewPassword", {
          params: {
            userid: this.$store.state.user.id,
            newPassword: this.newPassword
          }
        })
        .then(response => {
          var passwordEqualToLastPassword = response.data;
          if (this.updatePassword && passwordEqualToLastPassword) {
            this.$toasted.show(
              "Password must be different than last password.",
              { type: "error" }
            );
          } else {
            let formData = new FormData();
            formData.append("_method", "PUT");
            if (this.newPhotoFile && this.updatePhoto) {
              formData.append("photo", this.newPhotoFile);
            }
            if(this.updateName){
              formData.set("name", this.newName);
            }
            if(this.updatePassword){
              formData.set("password", this.newPassword);
            }
            if (this.$store.state.user.type == "u" && this.updateNif) {
              formData.set("nif", this.newNif);
            }
            //Axios.post although the formdata sets its as a PUT (apped)
            axios
              .post("api/users/" + this.$store.state.user.id, formData)
              .then(response => {
                this.logout();
              })
              .catch(function(error) {
                this.$toasted.show("Error updating user.", { type: "error" });
                console.log(error);
              });
          }
        });
    },
    validateName: function(name) {
      if (!name) {
        return false;
      }
      var re = /^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/;
      return re.test(name) && name.length >= 3;
    },
    previewImage: function(event) {
      var input = event.target;
      // Ensure that you have a file before attempting to read it
      if (input.files && input.files[0]) {
        // create a new FileReader to read this image and convert to base64 format
        var reader = new FileReader();
        // Define a callback function to run, when FileReader finishes its job
        reader.onload = e => {
          // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
          // Read image as base64 and set to imageData
          //console.log(e.target.result);
          this.newPhotoImage = e.target.result;
        };
        // Start the reader job - read file as a data url (base64 format)
        reader.readAsDataURL(input.files[0]);
        this.newPhotoFile = input.files[0];
      }
    },
    logout() {
      this.showMessage = false;
      //Todo implement sockets
      this.$socket.emit("logout", this.$store.state.user);
      axios
        .post("api/logout")
        .then(response => {
          this.$store.commit("clearUserAndToken");
          this.typeofmsg = "alert-success";
          this.message = "User has logged out correctly";
          this.showMessage = true;
          this.$router.push({ path: "/welcome" });
        })
        .catch(error => {
          this.$store.commit("clearUserAndToken");
          this.typeofmsg = "alert-danger";
          this.message =
            "Logout incorrect. But local credentials were discarded";
          this.showMessage = true;
          console.log(error);
        });
    }
  },
  computed:{
    updateAnything(){
      return this.updateName || this.updatePassword || this.updatePhoto || (this.$store.state.user.type == 'u' && this.updateNif);
    }
  }
};
</script>

<style>
</style>