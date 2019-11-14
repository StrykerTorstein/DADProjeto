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
        value
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
        value
      />
    </div>
    <div class="form-group">
      <label for="inputAge">Age</label>
      <input
        type="number"
        class="form-control"
        v-model="currentUser.age"
        name="age"
        id="inputAge"
        placeholder="Age"
        value
      />
    </div>
    <!--
    <div class="form-group">
      <label for="department_id">Department:</label>
      <select
        class="form-control"
        id="department_id"
        name="department_id"
        v-model="currentUser.department_id"
      >
        <option
          v-for="department in departments"
          :key="department.id"
          v-bind:value="department.id"
        >{{ department.name }}</option>
      </select>
    </div>
    -->

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
      //departments:[]
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