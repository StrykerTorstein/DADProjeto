
<template>
  <div>
    <br>
    <h1>Balance: {{ balance }}€</h1>
    <div class="jumbotron">
      <h1>Movements</h1>
    </div>
    <div>
      <div class="row">
        <div class="col-md-3"> 
          <div class="form-group">
              <label for="inputName">Id</label>
              <input type="text" class="form-control" v-model="filter.id"/>
          </div>
        </div>
        <div class="col-md-3"> 
          <div class="form-group">
              <label for="movement_id">Type</label>
              <select class="form-control" v-model="filter.type">
                <option value='' selected> -- Type -- </option>
                <option v-for="(item, key) in movementTypes" v-bind:key="key" :value="key"> {{item}} </option>
              </select>
          </div>
        </div>
        <div class="col-md-3"> 
          <div class="form-group">
              <label for="category_id">Category</label>
              <select class="form-control" id="name" name="name" v-model="movements.category_id" >
                  <option value='' selected> -- Category -- </option>
                  <option v-for="item in movements" :key="item.id" v-bind:value="item.name"> {{ item.name }} </option>
              </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              <label for="inputDate">Date Interval</label>
              <div>
              <input type="date" class="form-control" v-model="filter.start_date" name="date" id="inputDate"/>
              </div>
              <br>
              <div>
              <input type="date" class="form-control" v-model="filter.end_date" name="date" id="inputDate"/>
              </div>
          </div>
        </div>
        <div class="col-md-3"> 
          <div class="form-group">
              <label for="movement_id">Type of Payment</label>
              <select name="type_payment" class="form-control" v-model="filter.type_payment">
                <option value='' selected> -- Type Of Payment -- </option>
                <option v-for="(item, key) in movementTypesOfPayment" v-bind:key="key" :value="key"> {{item}} </option>
              </select> 
          </div>
        </div>
        <div class="col-md-3"> 
          <div class="form-group">
	          <label for="inputName">Email</label>
	            <input type="text" class="form-control" v-model="filter.email"/>
	        </div>
        </div>
      </div>
    </div>
  <div>
      <v-btn small color="primary" id="btn" v-on:click.prevent="getFilter()">Filter</v-btn>
      <v-btn small color="primary" id="btn" v-on:click.prevent="getMovements()">Clear Filters</v-btn>
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
    <v-btn v-if="movements.links.prev" small color="primary" id="btn" v-on:click.prevent="getMovements(movements.links.prev)">Previous</v-btn>
    <v-btn v-if="movements.links.next" small color="primary" id="btn" v-on:click.prevent="getMovements(movements.links.next)">Next</v-btn>
  </div>
</template>

<script>
import movementDetails from "./movementDetails";
export default {
  props: ["users"],
  data: () => {
    return {
      currentUser: null,
      user:null,
      movements: {links:{}, meta:{}},
      movement: undefined,
      filter:{},
      movementTypes:{
        e: "Expense",
        i: "Income"
      },
      movementTypesOfPayment:{
        c: "Cash",
        bt: "Bank Transfer",
        mb: "MB Payment"
      },
      balance: ""
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
        //console.log(this.movements);
      });
    },
    getMovementsPages: function(url){
      axios.post(url, this.filter).then(response => {
        this.movements = response.data;
      });
    },
    getFilter: function(){
      axios.post("api/" + this.user.id + "/movements", this.filter).then(response => {
        this.movements=response.data;
      })
    },
    getBalance: function(){
      axios.get('api/wallet/'+this.user.id+'/balance')
          .then(response=>{
              this.balance = response.data;
          })
          .catch(error => {                        
              console.log(error);
      })
    },
  },
  components: {
    "movement-details": movementDetails
  },
  mounted() {
    this.$store.commit("loadTokenAndUserFromSession");
    this.user = this.$store.state.user;
    this.getMovements();
    this.getFilter();
    this.getBalance();
  },
  sockets: {
    movementReceived(dataFromServer) {
      //console.log("Movement received!");
      this.getMovements();
      //let name = dataFromServer[1] === null ? 'Unknown' : dataFromServer[1].name;
      //this.$toasted.show('Message "' + dataFromServer[0]+ '" sent from "' + name + '"');
    }
  }
};
</script>

<style>
  #btn{
    color:black;
    margin-bottom: 20px;
    margin-right: 20px;
  }
</style>