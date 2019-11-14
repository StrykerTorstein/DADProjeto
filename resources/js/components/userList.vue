<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Age</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.id" :class="{active: currentUser === user}">
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.age }}</td>
        <td>
          <a class="btn btn-sm btn-primary" v-on:click.prevent="editUser(user)">Edit</a>
          <a class="btn btn-sm btn-danger" v-on:click.prevent="deleteUser(user)">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ["users"],
  data: () => {
    return {
      currentUser: null
    };
  },
  methods: {
    editUser(user) {
      this.currentUser = user;
      this.$emit("edit-user", user);
    },
    deleteUser(user) {
      this.currentUser = user;
      this.$emit("delete-user", user);
    }
  }
};
</script>

<style scoped>
</style>