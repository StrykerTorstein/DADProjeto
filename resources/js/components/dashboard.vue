<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div>
      <v-img v-if="photo" height="256px" width="256px" :src="photo"></v-img>
    </div>
    <div v-if="this.$store.state.user.type == 'o'">
      <v-btn class="btn btn-primary" v-on:click.prevent="registerIncome = true">Register Income</v-btn>
      <div v-if="registerIncome">
        <div>
          Email
          <input
            type="email"
            class="form-control"
            v-model="movementWalletEmail"
            name="email"
            id="inputMovementWalletEmail"
            placeholder="test@mail.pt"
          />
          Value (€)
          <input
            type="number"
            class="form-control"
            v-model="movementAmount"
            name="ammount"
            id="inputMovementAmount"
            min="0.01"
            max="5000"
          />
          Payment Type
          <select class="form-control m-bot15" name="movementPaymentType" v-model="selectedMovementPaymentType">
            <option v-for="(item, key) in movementPaymentTypes" v-bind:key="key" :value="key">{{item}}</option>
          </select>
          <div v-show="selectedMovementPaymentType == 'bt'">
          IBAN
          <input
            type="text"
            class="form-control"
            v-model="movementIBANCapitalLetters"
            name="ibanLetters"
            id="inputmovementIBANCapitalLetters"
            placeholder="XX"
            :maxlength="2"
          />
          <input
            type="text"
            class="form-control"
            v-model="movementIBAN23Digits"
            name="ibanNumbers"
            id="inputmovementIBAN23Digits"
            placeholder="12345678901234567890123"
          />
          <v-alert type="warning" v-if="movementIbanDigitShowSizeWarning">Must have 23 digits and be a number!</v-alert>
          </div>
        </div>
        <v-btn class="btn btn-danger" v-on:click.prevent="registerIncome = false">Cancel</v-btn>
        <v-btn class="btn btn-success" v-on:click.prevent="registerIncomeMovement()">Register</v-btn>
      </div>
    </div>
    <!--<v-btn class="btn btn-primary" v-on:click.prevent="debug()">Debug</v-btn>-->
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      title: "Dashboard",
      photo: null,
      registerIncome: false,
      movementWalletEmail: null,
      movementAmount: 0.01,
      movementPaymentTypes: {
        "c": "Cash",
        "bt": "Bank Transfer",
      },
      selectedMovementPaymentType: "c",
      movementIBANCapitalLetters: null,
      movementIbanDigitShowSizeWarning: false,
      movementIBAN23Digits: null,
      movementIBAN: null
    };
  },
  methods: {
    debug: function() {
      //Put this in a creation of a payment
    },
    registerIncomeMovement() {
      console.log("Success");
      /*
      let formData = new FormData();
      formData.append("mambojambo", "PART ALL NITE");
      axios
        .post("api/movements/payment", {name:"hello"})
        .then(() => {
          
        })
        .catch(function(error) {
          if(error.response.status == 403){
            console.log("User is not type operator!");
          }
          console.log(error);
        });
        */
    }
  },
  mounted() {
    if (this.$store.state.user) {
      this.title = `'${this.$store.state.user.name}' Dashboard`;
      if (
        this.$store.state.user.photo !== "null" &&
        this.$store.state.user.photo
      ) {
        this.photo = "storage/fotos/" + this.$store.state.user.photo;
      }
    } else {
      console.log("Not logged in, cannot access the dashboard page!");
      this.$router.push({ path: "/welcome" });
    }
  },
  watch: {
    movementAmount: function() {
      if (this.movementAmount < 0.01) {
        this.movementAmount = 0.01;
      }
      if (this.movementAmount > 5000) {
        this.movementAmount = 5000;
      }
    },
    movementIBAN23Digits: function(){
      var str = `${this.movementIBAN23Digits}`;
      this.movementIbanDigitShowSizeWarning = str.length != 23 || isNaN(str);
    },
    movementIBANCapitalLetters: function(){
      this.movementIBANCapitalLetters = this.movementIBANCapitalLetters.toUpperCase();
    }
  }
};
</script>

<style>
</style>