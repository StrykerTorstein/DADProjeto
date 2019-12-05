<template>
  <div> 
    <div class="jumbotron" >
		    <h1>Movements</h1>
		</div>
  <table class="table table-striped">
    <thead>
      <tr>
        <th><font size="5">Id</font></th>
        <th><font size="5">Type</font></th>
        <th><font size="5">Type of Payment</font></th>
        <th><font size="5">Email</font></th>
        <th><font size="5">Category</font></th>
        <th><font size="5">Date</font></th>
        <th><font size="5">Start Balance</font></th>
        <th><font size="5">End Balance</font></th>
        <th><font size="5">Value</font></th>
        <th><font size="5">Actions</font></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="movement in movements" :key="movement.id" :class="{active: currentUser === user}">
        <th>{{movement.id}}</th>       
        <th>{{movement.type}}</th> 
        <th>{{movement.type_payment}}</th> 
        <th>{{movement.email}}</th>
        <th>{{movement.category}}</th>
        <th>{{movement.date}}</th> 
        <th>{{movement.start_balance}}€</th> 
        <th>{{movement.end_balance}}€</th> 
        <th>{{movement.value}}€</th>
        <td>
            <a class="btn btn-sm btn-primary" id="btn" v-on:click.prevent="movementDetails(movement)">Details</a>
		    </td> 
      </tr>
    </tbody>
  </table>
    <div> 
      <movement-details :movement="movement" v-if="movement"></movement-details> 
    </div> 
  </div>
</template>

<script>
import movementDetails from './movementDetails';
export default {
  props: ["users"],
  data: () => {
    return {
      currentUser: null,
      movements: null,
      movement: undefined
      
    };
  },
  methods: {
    movementDetails(movement) {
      this.movement = movement; 
    },
    deleteUser(user) {
      this.currentUser = user;
      this.$emit("delete-user", user);
    },
    getMovements: function() {
      axios.get("api/"+ this.$data.user.id +"/movements").then(response => {
        this.movements = response.data.data;
      });
    }
  },
  components:{
    'movement-details': movementDetails
  },
  mounted() {
    this.$store.commit("loadTokenAndUserFromSession");
    this.$data.user = this.$store.state.user;
    this.getMovements();
  },
};
</script>

<style>
  #btn{
    color:white;
  }
</style>