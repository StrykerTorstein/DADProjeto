<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Age</th>
        <th>Department</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.id" :class="{active: currentUser === user}">
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.age }}</td>
        <td>{{ user.department }}</td>
        <td>
          <a class="btn btn-sm btn-success" v-on:click.prevent="definePlayer(user,1)">P1</a>
          <a class="btn btn-sm btn-success" v-on:click.prevent="definePlayer(user,2)">P2</a>
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
    },
    definePlayer(user, playerNumber) {
      switch (playerNumber) {
        case 1:
          this.$root.player1 = user;
          return;
        case 2:
          this.$root.player2 = user;
          return;
      }
    }
  }
};
</script>

<style scoped>
</style>