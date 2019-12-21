<template>
  <div>
    <div>
      <button class="btn btn-sm btn-primary" v-on:click.prevent="addUser()">Add User</button>
    </div>
    <div class="row">
      <div class="col-md-3">
          <div class="form-group">
            <label for="user_id">Type of User</label>
            <select name="type" class="form-control" v-model="filter.type">
              <option value selected>All</option>
              <option
                v-for="(item, key) in userTypes"
                v-bind:key="key"
                :value="key"
              >{{item}}</option>
            </select>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group">
            <label for="inputName">Name</label>
            <input type="text" class="form-control" v-model="filter.name" />
          </div>
        </div>
      <div class="col-md-3">
          <div class="form-group">
            <label for="inputName">Email</label>
            <input type="text" class="form-control" v-model="filter.email" />
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group">
            <label for="user_id">Status</label>
            <select name="active" class="form-control" v-model="filter.active">
              <option value selected>Any</option>
              <option
                v-for="(item, key) in userActive"
                v-bind:key="key"
                :value="key"
              >{{item}}</option>
            </select>
          </div>
      </div>
    </div>
    <div>
      <v-btn small color="primary" id="btn" v-on:click.prevent="getUsers()">Filter</v-btn>
      <v-btn small color="primary" id="btn" v-on:click.prevent="clearFilters()">Clear Filters</v-btn>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th></th>
          <th><font size="5">Name</font></th>
          <th><font size="5">Email</font></th>
          <th><font size="5">Type</font></th>
          <th><font size="5">Active</font></th>
          <th><font size="5">Balance</font></th>
          <th><font size="5">Actions</font></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" :class="{active: currentUser === user}">
          <td><img class="img" height="80px" width="80px" :src="photo(user)"></td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ userType(user)}}</td>
          <td v-if="user.type == 'u'">
            <button class="btn btn-sm btn-primary" v-if="user.active == 0" v-on:click="activate(user)">Activate</button>
            <button class="btn btn-sm btn-primary" v-if="user.active == 1 && user.wallet.balance == 0" v-on:click="activate(user)">Deactivate</button>
            <button class="btn btn-sm btn-primary" v-if="user.active == 1 && user.wallet.balance > 0" v-on:click="activate(user)"  disabled>Deactivate</button>
          </td>
          <td v-else>
            {{isActive(user)}}
          </td>
          <td>{{ balance(user) }}</td>
          <td>
            <button v-if="$store.state.user.id == user.id || user.type == 'u'" class="btn btn-sm btn-danger"  v-on:click.prevent="deleteUser(user)" disabled>Delete</button>
            <button v-else class="btn btn-sm btn-danger"  v-on:click.prevent="deleteUser(user)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <v-btn
      v-if="meta.current_page - 1 > 0"
      small
      color="primary"
      id="btn"
      v-on:click.prevent="getPreviousPage()"
    >Previous</v-btn>
    <v-btn
      v-if="meta.current_page + 1 <= meta.last_page"
      small
      color="primary"
      id="btn"
      v-on:click.prevent="getNextPage()"
    >Next</v-btn>
  </div>
</template>

<script>
export default {
  //props: ["users"],
  data: () => {
    return {
      currentUser: null,
      filter: {},
      users: null,
      userTypes: {
        a: "Administrator",
        o: "Operator",
        u: "User"
      },
      userActive: {
        1: "Active",
        0: "Inactive",
      },
      meta: {},
    };
  },
  methods: {
    addUser() {
      this.$router.push({ path: '/users/add' });
    },
    activate(user){
        axios
        .put("api/users/" + user.id + "/active", user)
        .then(response => {
          this.getUsers();
        });
    },
    editUser(user) {
      this.currentUser = user;
      this.$emit("edit-user", user);
    },
    deleteUser(user) {
      this.currentUser = user;
      this.$emit("delete-user", user);
    },
    photo(user) {
      if(user.photo != null){
        return "storage/fotos/" + user.photo;
      }
      return null;
    },
    isActive(user){
      if(user.active == 1){
        return "Active";
      }else{
        return "Inactive";
      }
    },
    userType(user){
      if(user.type == "a"){
        return "Administrator";
      }else if(user.type == "o"){
        return "Operator";
      }else{
        return "User";
      }
    },
    balance(user){
      if(user.wallet == null){
        return "--------";
      }else{
        if(user.wallet.balance > 0){
          return "Has money"
        }
        if(user.wallet.balance == 0){
          return "Empty"
        }
      }
    },
    getUsers(page) {
      //console.log("Fetching page: " + page);
      let params = Object.assign({}, this.filter);
      if (page) {
        params.page = page;
      }
      for (var key in params) {
        if (params[key] == "") params[key] = null;
      }
      //console.log(params);
      axios
        .get("api/users", { params: params })
        .then(response => {
          this.users = response.data.data;
          this.meta = response.data.meta;
        });
    },
    getNextPage: function() {
      
      this.getUsers(this.meta.current_page + 1);
    },
    getPreviousPage: function() {
      this.getUsers(this.meta.current_page - 1);
    },
    clearFilters() {
      this.filter = {};
      this.getUsers();
    },
  },
  created() {
      this.getUsers();
  }
};
</script>

<style scoped>
</style>