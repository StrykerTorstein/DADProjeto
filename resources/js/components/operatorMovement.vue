<template>
  <div class="jumbotron">
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
        Category
        <select
          class="form-control m-bot15"
          name="movementCatType"
          v-model="movementCategoryName"
        >
          <option v-for="item in categoryNames" v-bind:key="item" :value="item">{{item}}</option>
        </select>
        Payment Type
        <select
          class="form-control m-bot15"
          name="movementPaymentType"
          v-model="selectedMovementPaymentType"
        >
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
          <v-alert
            type="warning"
            v-if="movementIbanDigitShowSizeWarning"
          >Must have 2 letters and 23 digits!</v-alert>
        </div>Description
        <input
          type="text"
          class="form-control"
          v-model="movementSourceDescription"
          name="inputMovementSourceDescription"
          id="movementSourceDescription"
          placeholder="Movement source description"
        />
      </div>
      <v-btn class="btn btn-danger" v-on:click.prevent="registerIncome = false">Cancel</v-btn>
      <v-btn class="btn btn-success" v-on:click.prevent="registerIncomeMovement()">Register</v-btn>
    </div>
  </div>
</template>

<script>
import { request } from "http";
export default {
  data: () => {
    return {
      registerIncome: false,
      movementWalletEmail: null,
      movementAmount: 0.01,
      movementPaymentTypes: {
        c: "Cash",
        bt: "Bank Transfer"
      },
      selectedMovementPaymentType: "c",
      movementIBANCapitalLetters: null,
      movementIbanDigitShowSizeWarning: false,
      movementIBAN23Digits: null,
      movementIBAN: null,
      movementSourceDescription: null,
      movementCategoryName: null,
      categoryNames: null
    };
  },
  methods: {
    debug: function() {
      //Put this in a creation of a payment
    },
    registerIncomeMovement() {
      if (this.movementIbanDigitShowSizeWarning === true) {
        console.log("Invalid Data to send.");
        return;
      }
      axios
        .get("api/wallets/exists/" + this.movementWalletEmail)
        .then(response => {
          if (response.data) {
            var walletId = response.data.id;
            var walletEmail = response.data.email;
            var walletBalance = response.data.balance;

            var movementType = this.selectedMovementPaymentType;
            var movementIban = null;
            if (
              this.movementPaymentTypes[movementType] ==
              this.movementPaymentTypes["bt"]
            ) {
              movementIban =
                this.movementIBANCapitalLetters + this.movementIBAN23Digits;
              if (movementIban.length != 25) {
                this.movementIbanDigitShowSizeWarning = true;
                return;
              }
            }
            var movementAmount = this.movementAmount;
            var movementSourceDescription = this.movementSourceDescription;
            var movementStartBalance = walletBalance;
            var movementEndBalance =
              Number(walletBalance) + Number(movementAmount);
            var movementWalletId = walletId;
            var movementCategoryTypeName = this.movementCategoryName;

            var movement = {
              wallet_id: walletId,
              type_payment: movementType,
              iban: movementIban,
              value: Number(movementAmount),
              source_description: movementSourceDescription,
              categoryName: movementCategoryTypeName,
              transfer: 0
            };
            //console.log("Posting Movement:");
            //console.log(movement);
            axios
              .post("api/movements/payment", movement)
              .then(response => {
                //console.log(response.data.userid);
                //Web socket force refresh on user id with email of wallet
                if (response.data.user) {
                  var user = response.data.user;
                  this.$socket.emit(
                    "sendMovement",
                    this.$store.state.user,
                    user
                  );
                }
              })
              .catch(function(err) {});
          } else {
            console.log("Wallet does NOT exist");
          }
        })
        .catch(function(err) {});
    },
    setCategoryNames() {
      axios
        .get("api/categories/names/" + "i")
        .then(response => {
          this.categoryNames = response.data;
          this.movementCategoryName = this.categoryNames[0];
        })
        .catch(function(error) {
          //Hardcoded as a safety measure (although not the best solution...)
          this.categoryNames = [
            "salary",
            "bonus",
            "royalties",
            "interests",
            "gifts",
            "dividends",
            "sales",
            "loan repayment",
            "loan",
            "other income"
          ];
        });
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
    this.setCategoryNames();
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
    movementIBAN23Digits: function() {
      var str = `${this.movementIBAN23Digits}`;
      this.movementIbanDigitShowSizeWarning = str.length != 23 || isNaN(str);
    },
    movementIBANCapitalLetters: function() {
      this.movementIBANCapitalLetters = this.movementIBANCapitalLetters.toUpperCase();
      var str = `${this.movementIBANCapitalLetters}`;
      this.movementIbanDigitShowSizeWarning =
        str.length != 2 || !isNaN(str) || !isNaN(str[0]) || !isNaN(str[1]);
    }
  },
  sockets: {
    movementSent(dataFromServer) {
      //Maybe install toasted?
      //Clear inputs
      //Close the register income button
      //Transfer all this onto a separate "operator" component
      console.log("Movement sent!");
    }
  }
};
</script>

<style>
</style>