<template>
  <div class="jumbotron">
    <h2>Edit User</h2>
    <div class="form-group">
      <label for="inputName">Name</label>
      <input
        type="text"
        class="form-control"
        v-model="currentUser.name"
        name="name"
        id="inputName"
        placeholder="Fullname"
        value readonly
      />
    </div>
    <div class="form-group">
      <label for="inputEmail">Email</label>
      <input
        type="email"
        class="form-control"
        v-model="currentUser.email"
        name="email"
        id="inputEmail"
        placeholder="Email address"
        value readonly
      />
    </div>
    <div class="form-group">
      <label for="inputType">User type</label>
        <select
          class="form-control m-bot15"
          name="userType"
          v-model="currentUser.type"
        >
          <option v-for="(item, key) in userTypes" v-bind:key="key" :value="key">{{item}}</option>
        </select>
    </div>
    <div class="form-group">
      <label for="inputState">State</label>
        <select
          class="form-control m-bot15"
          name="userState"
          v-model="currentUser.active"
        >
          <option v-for="(item, key) in userActive" v-bind:key="key" :value="key">{{item}}</option>
        </select>
    </div>
    <div class="form-group">
      <a class="btn btn-primary" v-on:click.prevent="saveUser()">Save</a>
      <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
    </div>
  </div>
</template>

<script>
export default {
  props:['currentUser'],
  data: () => {
    return {
      userTypes: {
        a: "Administrator",
        o: "Operator",
        u: "User"
      },
      userActive: {
        1: "Active",
        0: "Inactive",
      },
    };
  },
  methods: {
    saveUser(user) {
      this.$emit("save-user",this.currentUser);
    },
    cancelEdit(user) {
      this.$emit("cancel-edit");
    }
  },mounted(){
    //Para mostrar que é possivel aceder a variaveis no root (vue.js file)
    //this.departments = this.$root.departments;
  }
};
</script>