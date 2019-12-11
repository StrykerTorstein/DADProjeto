
<template>
  <div> 
    <div class="jumbotron" >
		    <h1>Movements</h1>
		</div>
    <div>
      <div class="form-group">
	        <label for="inputName">Id</label>
	        <input
	            type="text" class="form-control" v-model="filter.id"/>
	    </div>
      <div class="form-group">
	        <label for="movement_id">Type</label>
	        <select class="form-control" id="movement_id" name="movement_id" v-model="filter.type" >
	            <!-- <option v-for="movement in movements" :key="movement.id" v-bind:value="movement.id"> {{ movement.type }} </option>-->
	        </select>
	    </div>
      <div class="form-group">
	        <label for="inputDate">Date Interval</label>
	        <input
	            type="date" class="form-control" v-model="filter.start_date"
	            name="date" id="inputDate"/>
          <br>
            <input
                type="date" class="form-control" v-model="filter.end_date"
                name="date" id="inputDate"/>
          <br>
	    </div>
      <div class="form-group">
	        <label for="movement_id">Category</label>
	        <select class="form-control" v-model="filter.category" >
	            <!--<option v-for="movement in movements" :key="movement.category" v-bind:value="movement.category"> {{ movement.category }} </option>-->
	        </select>
	    </div>
    </div>

    <div>
        <v-btn small color="primary" id="btn" v-on:click.prevent="getFilter()">Filter</v-btn>
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
      <tr v-for="movement in movements.data" :key="movement.id" >
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
            <v-btn small color="primary" id="btn" v-on:click.prevent="movementDetails(movement)">Details</v-btn>
		    </td> 
      </tr>
    </tbody>
  </table>
    <div> 
      <movement-details :movement="movement" v-if="movement"></movement-details> 
    </div> 
  <div>
  </div>
    <v-btn v-if="movements.links.prev" small color="primary" id="btn" v-on:click.prevent="getMovementsPages(movements.links.prev)">Previous</v-btn>
    <v-btn v-if="movements.links.next" small color="primary" id="btn" v-on:click.prevent="getMovementsPages(movements.links.next)">Next</v-btn>
  </div>
</template>

<script>
import movementDetails from './movementDetails';
export default {
  props: ["users"],
  data: () => {
    return {
      currentUser: null,
      user:null,
      movements: {links:{}, meta:{}},
      movement: undefined,
      filter:{}
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
      axios.get("api/"+ this.user.id +"/movements").then(response => {
        this.movements = response.data;
        console.log(this.movements);
      });
    },
    getMovementsPages: function(url){
      axios.get(url).then(response => {
        this.movements = response.data;
        
      });
    },
    getFilter: function(){
      axios.post("api/" + this.user.id + "/movements", this.filter).then(response => {
        this.movements=response.data;
      })
    }
  },
  components:{
    'movement-details': movementDetails
  },
  mounted() {
    this.$store.commit("loadTokenAndUserFromSession");
    this.user = this.$store.state.user;
    this.getMovements();
  },
};
</script>

<style>
  #btn{
    color:black;
  }
</style>