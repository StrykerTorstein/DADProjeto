<template>
  <div>
    <br />
    <h1>Balance: {{ balance }}€</h1>
    <div class="jumbotron">
      <h1>Movements</h1>
    </div>
    <div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="inputName">Id</label>
            <input type="text" class="form-control" v-model="filter.id" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="movement_id">Type</label>
            <select class="form-control" v-model="selectedMovementType">
              <option value selected>ALL</option>
              <option
                v-for="item in movementTypes"
                :value="item"
                :key="item"
              >{{getTitleForType(item).toUpperCase()}}</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <!-- problem , sometimes categoryList is null-->
          <div v-if="categoryList && categoryList.length > 0" class="form-group">
            <label for="category_id">Category</label>
            <select class="form-control" id="name" name="name" v-model="filter.category">
              <option value selected>ALL</option>
              <option v-for="item in categoryList" :key="item.id">{{ item.name.toUpperCase() }}</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="inputDate">Date Interval</label>
            <div>
              <input
                type="date"
                class="form-control"
                v-model="filter.start_date"
                name="date"
                id="inputDate"
              />
            </div>
            <br />
            <div>
              <input
                type="date"
                class="form-control"
                v-model="filter.end_date"
                name="date"
                id="inputDate"
              />
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="movement_id">Type of Payment</label>
            <select name="type_payment" class="form-control" v-model="filter.type_payment">
              <option value selected>-- Type Of Payment --</option>
              <option
                v-for="(item, key) in movementTypesOfPayment"
                v-bind:key="key"
                :value="key"
              >{{getTitleForTypePayment(item)}}</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="inputName">Email</label>
            <input type="email" class="form-control" v-model="filter.email" />
          </div>
        </div>
      </div>
    </div>
    <div>
      <v-btn small color="primary" id="btn" v-on:click.prevent="getMovements()">Filter</v-btn>
      <v-btn small color="primary" id="btn" v-on:click.prevent="clearFilters()">Clear Filters</v-btn>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>
            <font size="5">Id</font>
          </th>
          <th>
            <font size="5">Type</font>
          </th>
          <th>
            <font size="5">Type of Payment</font>
          </th>
          <th>
            <font size="5">Email</font>
          </th>
          <th>
            <font size="5">Category</font>
          </th>
          <th>
            <font size="5">Date</font>
          </th>
          <th>
            <font size="5">Start Balance</font>
          </th>
          <th>
            <font size="5">End Balance</font>
          </th>
          <th>
            <font size="5">Value</font>
          </th>
          <th>
            <font size="5">Actions</font>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="movement in movements" :key="movement.id">
          <th>{{movement.id}}</th>
          <th>{{getTitleForType(movement.type)}}</th>
          <th>{{getTitleForTypePayment(movement.type_payment)}}</th>
          <th>{{movement.email}}</th>
          <th>{{movement.category}}</th>
          <th>{{movement.date}}</th>
          <th>{{movement.start_balance}}€</th>
          <th>{{movement.end_balance}}€</th>
          <th>{{movement.value}}€</th>
          <td>
            <v-btn
              small
              color="primary"
              id="btn"
              v-on:click.prevent="movementDetails(movement)"
            >Details</v-btn>
          </td>
        </tr>
      </tbody>
    </table>
    <div>
      <movement-details :movement="movement" v-if="movement" @edited-movement="onMovementEdited"></movement-details>
    </div>
    <div></div>
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
import movementDetails from "./movementDetails";
export default {
  props: ["users"],
  data: () => {
    return {
      currentUser: null,
      user: null,
      movements: [],
      meta: {},
      movement: undefined,
      filter: { type: "i" },
      movementTypes: {},
      movementTypesOfPayment: {
        c: "Cash",
        bt: "Bank Transfer",
        mb: "MB Payment"
      },
      balance: "",
      allCategories: {},
      categoryList: null,
      selectedMovementType: null
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
    getMovements(page) {
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
        .get("api/" + this.$store.state.user.id + "/movements", {
          params: params
        })
        .then(response => {
          this.movements = response.data.data;
          this.meta = response.data.meta;
        });
    },
    getNextPage: function() {
      this.getMovements(this.meta.current_page + 1);
    },
    getPreviousPage: function() {
      this.getMovements(this.meta.current_page - 1);
    },
    clearFilters() {
      this.filter = {};
      this.getMovements();
    },
    getBalance: function() {
      axios
        .get("api/wallet/" + this.$store.state.user.id + "/balance")
        .then(response => {
          this.balance = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getCategories: function(type) {
      axios.get("api/categories/names").then(response => {
        this.allCategories = response.data;
      });
    },
    getTypes: function() {
      axios.get("api/movements/types").then(response => {
        this.movementTypes = response.data;
      });
    },
    getTitleForType(type) {
      if (type == "e") return "Expense";
      if (type == "i") return "Income";
      return type;
    },
    getTitleForTypePayment(type_payment) {
      if (type_payment == "c") return "Cash";
      if (type_payment == "bt") return "Bank Transfer";
      if (type_payment == "mb") return "MB Payment";

      return type_payment;
    },
    onMovementEdited() {
      this.$toasted.show("Movement Edited");
      this.movement = null;
      this.getMovements();
      this.getBalance();
      this.getCategories();
      this.getTypes();
    }
  },
  components: {
    "movement-details": movementDetails
  },
  mounted() {
    this.getMovements();
    this.getBalance();
    this.getCategories();
    this.getTypes();
  },
  computed: {
    categoryList: function() {
      console.log("computing categories for" + this.filter.type);

      // If there is not type selected return an empty array
      if (!this.filter.type) return [];

      //In case there is a type selected, return the categories for that type
      return this.allCategories.filter(
        category => category.type == this.filter.type
      );
    }
  },
  sockets: {
    movementReceived(dataFromServer) {
      //console.log("Movement received!");
      this.getMovements();
      this.getBalance();
      //let name = dataFromServer[1] === null ? 'Unknown' : dataFromServer[1].name;
      //this.$toasted.show('Message "' + dataFromServer[0]+ '" sent from "' + name + '"');
    }
  },
  computed: {
    allMovements() {
      if (this.movements.data) {
        console.log(this.movements.meta.current_page);
        return this.movements.data;
      } else {
        axios
          .get("api/" + this.$store.state.user.id + "/movements")
          .then(response => {
            this.movements = response.data;
            return this.movements.data;
          });
      }
    }
  },
  watch: {
    filter() {
      this.categoryList = this.allCategories.filter(
        category => category.type == this.filter.type
      );
    },
    selectedMovementType() {
      if (
        !this.selectedMovementType ||
        this.selectedMovementType.length === 0
      ) {
        this.selectedMovementType = null;
      }
      this.filter.type = this.selectedMovementType;
      this.categoryList = this.allCategories.filter(
        category => category.type == this.filter.type
      );
      //filter.category = this.categoryList[1];
    }
  }
};
</script>

<style>
#btn {
  color: black;
  margin-bottom: 20px;
  margin-right: 20px;
}
</style>