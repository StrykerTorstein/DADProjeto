<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    
    <!-- here we map the variable users to the 'props' users on userList.vue -->
    <!-- @ is the same as v-on: -->
    <user-list v-bind:users="users" @edit-user="editUser" @delete-user="deleteUser"></user-list>

    <div class="alert alert-success" v-if="showSuccess">
      <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
      <strong>{{ successMessage }}</strong>
    </div>
    <!-- v-bind:componentVariable="localVariable" -->
    <user-edit 
        v-if="editingUser"
        v-bind:currentUser="currentUser"
        @save-user="saveUser" 
        @cancel-edit="cancelEdit">
    </user-edit>
  </div>
</template>

<script>

import UserList from './userList';

import UserEdit from './userEdit';
import movementDetailsVue from './movementDetails.vue';

export default {
  data: () => {
    return {
      title: "Users",
      editingUser: false,
      showSuccess: false,
      showFailure: false,
      successMessage: "",
      failMessage: "",
      currentUser: null,
      users: []
    };
  },
  methods: {
    editUser: function(user) {
      this.currentUser = Object.assign({},user);
      this.editingUser = true;
      this.showSuccess = false;
    },

    deleteUser: function(user) {
      if(this.$store.state.user.id != user.id){
        axios.delete("api/users/" + user.id).then(response => {
          this.showSuccess = true;
          this.successMessage = "User Deleted";
          this.getUsers();
        });
      }else{
        this.failMessage = "Ola";
      }
    },
    saveUser: function(user) {
      this.editingUser = false;
      axios
        .put("api/users/" + user.id, user)
        .then(response => {
          this.showSuccess = true;
          this.successMessage = "User Saved";
          // Copies response.data.data properties to this.currentUser
          // without changing this.currentUser reference
          Object.assign(this.currentUser, response.data.data);
          Object.assign(this.users.find(u=>u.id == response.data.data.id),response.data.data);
          this.currentUser = null;
          this.editingUser = false;
        });
    },

    cancelEdit: function() {
      this.showSuccess = false;
      this.editingUser = false;
      axios.get("api/users/" + this.currentUser.id).then(response => {
        console.dir(this.currentUser);
        // Copies response.data.data properties to this.currentUser
        // without changing this.currentUser reference
        Object.assign(this.currentUser, response.data.data);
        console.dir(this.currentUser);
        this.currentUser = null;
      });
    },
    getUsers: function() {
      axios.get("api/users").then(response => {
        this.users = response.data.data;
      });
    }
  },
  mounted() {
    this.getUsers();
  },
  components:{
      'user-list' : UserList,
      'user-edit' : UserEdit,
      //'movement-details' : movementDetails,
  }
};
</script>

<style>
</style>