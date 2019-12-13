<template>
  <div class="jumbotron">
    <v-btn class="btn btn-primary" v-on:click.prevent="registerExpense = true">Register Expense</v-btn>
    <div v-if="registerExpense">
      <h1>Current Balance: {{ balance }}€</h1>Type
      <select class="form-control m-bot15" name="movementPaymentType" v-model="expenseType">
        <option v-for="item in expenseTypes" v-bind:key="item" :value="item">{{item}}</option>
      </select>
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
      Balance After: {{balanceAfter}} €
      <br />
      <v-alert type="warning" v-if="overBudget">Value must be below or equal to your balance!</v-alert>Category
      <select
        class="form-control m-bot15"
        name="movementCatType"
        v-model="movementCategoryName"
      >
        <option v-for="item in categoryNames" v-bind:key="item" :value="item">{{item.toUpperCase()}}</option>
      </select>
      Description
      <input
        type="text"
        class="form-control"
        v-model="movementDescription"
        name="inputmovementDescription"
        id="movementDescription"
        placeholder="Movement description"
      />
      <!-- External -->
      <div v-if="expenseType == expenseTypes[0]">
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
        </div>
        <div v-show="selectedMovementPaymentType == 'mb'">
          MB Entity Code
          <input
            type="number"
            class="form-control"
            v-model="movementMBEntityCode"
            name="ammount"
            id="inputmovementMBEntityCode"
            min="10000"
            max="99999"
          />
          <v-alert type="warning" v-if="invalidMovementMBEntityCode">Entity Code is 5 digits</v-alert>MB Payment Reference
          <input
            type="number"
            class="form-control"
            v-model="movementMBPaymentReference"
            name="ammount"
            id="inputmovementMBPaymentReference"
            min="100000000"
            max="999999999"
          />
          <v-alert
            type="warning"
            v-if="invalidMovementMBPaymentReference"
          >Payment Reference is 9 digits</v-alert>
        </div>
      </div>
      <!-- Transfer -->
      <div v-if="expenseType == expenseTypes[1]">
        Wallet Email
        <input
          type="email"
          class="form-control"
          v-model="movementWalletEmail"
          name="email"
          id="inputMovementWalletEmail"
          placeholder="user2@mail.pt"
        />
        <v-alert
          type="warning"
          v-if="!walletEmailExists || movementWalletEmail == null"
        >Non existent wallet email</v-alert>Source Description
        <input
          type="text"
          class="form-control"
          v-model="movementSourceDescription"
          name="inputMovementSourceDescription"
          id="movementSourceDescription"
          placeholder="Movement source description"
        />
      </div>
      <v-btn class="btn btn-danger" v-on:click.prevent="registerExpense = false">Cancel</v-btn>
      <v-btn class="btn btn-success" v-on:click.prevent="registerExpenseMovement()">Register</v-btn>
    </div>
  </div>
</template>

<script>
import { request } from "http";
export default {
  data: () => {
    return {
      balance: 0,
      registerExpense: false,
      expenseType: "External",
      expenseTypes: ["External", "Transfer"],
      movementAmount: 0.01,
      overBudget: false,
      categoryNames: null,
      movementCategoryName: null,
      movementDescription: null,
      movementSourceDescription: "",
      movementPaymentTypes: {
        mb: "MB Payment",
        bt: "Bank Transfer"
      },
      selectedMovementPaymentType: "mb",
      movementIBANCapitalLetters: null,
      movementIbanDigitShowSizeWarning: false,
      movementIBAN23Digits: null,
      movementIBAN: null,
      movementMBEntityCode: 10000,
      invalidMovementMBEntityCode: false,
      movementMBPaymentReference: 100000000,
      invalidMovementMBPaymentReference: false,
      movementWalletEmail: null,
      walletEmails: null,
      walletEmailExists: false,
      balanceAfter: 0
    };
  },
  methods: {
    registerExpenseMovement() {
      if (this.overBudget) {
        this.toastedError("You are over budget! Decrease the value.");
        return;
      }
      var external = this.expenseType == this.expenseTypes[0];
      var transfer = this.expenseType == this.expenseTypes[1];
      if (external) {
        if (this.selectedMovementPaymentType == "bt") {
          if (this.movementIbanDigitShowSizeWarning) {
            this.toastedError("Invalid IBAN");
            return;
          }
        } else if (this.selectedMovementPaymentType == "mb") {
          if (this.invalidMovementMBPaymentReference) {
            this.toastedError("Invalid MB Payment Reference");
            return;
          }
          if (this.invalidMovementMBEntityCode) {
            this.toastedError("Invalid MB Entity Code");
            return;
          }
        }
        this.registerExternalExpense();
      } else if (transfer) {
        if (!this.walletEmailExists) {
          this.toastedError(
            `Email "${this.movementWalletEmail}" has no associated wallet!`
          );
          return;
        }
        if (this.movementWalletEmail == this.$store.state.user.email) {
          this.toastedError(`You can't transfer money to yourself.`);
          return;
        }
        this.registerTranferExpense();
      } else {
        this.toastedError("Select an expense type");
      }
    },
    getBalance() {
      axios
        .get("api/wallet/" + this.$store.state.user.id + "/balance")
        .then(response => {
          this.balance = response.data;
          this.movementAmount = 0.01;
          this.balanceAfter = this.balance - this.movementAmount;
          this.balanceAfter = Math.round(this.balanceAfter * 100) / 100;
        })
        .catch(error => {
          console.log(error);
        });
    },
    setCategoryNames() {
      axios
        .get("api/categories/names/" + "e")
        .then(response => {
          this.categoryNames = response.data;
          this.movementCategoryName = this.categoryNames[0];
        })
        .catch(function(error) {
          //Hardcoded as a safety measure (although not the best solution...)
          this.categoryNames = ["ERR"];
        });
    },
    getAllWalletEmails() {
      axios.get("api/wallets/allemails").then(response => {
        this.walletEmails = response.data;
      });
    },
    toastedError(msg) {
      this.$toasted.show(msg, {
        type: "error"
      });
    },
    registerExternalExpense() {
      var formData = new FormData();
      formData.set("srcEmail", this.$store.state.user.email);
      formData.set("value", this.movementAmount);
      formData.set("type_payment", this.selectedMovementPaymentType);
      formData.set("categoryName", this.movementCategoryName);
      formData.set("description", this.movementDescription);
      if (this.selectedMovementPaymentType == "bt") {
        //IBAN
        this.movementIBAN =
          this.movementIBANCapitalLetters + this.movementIBAN23Digits;
        formData.set("iban", this.movementIBAN);
      } else if (this.selectedMovementPaymentType == "mb") {
        formData.set("mb_entity_code", this.movementMBEntityCode);
        formData.set("mb_payment_reference", this.movementMBPaymentReference);
      }
      axios.post("api/movements/expense/external", formData).then(response => {
        //console.log(response.data);
        var user = response.data.user; //Me
        this.$socket.emit("sendMovement", this.$store.state.user, user);
        this.getBalance();
      });
    },
    registerTranferExpense() {
      var formData = new FormData();
      formData.set("srcEmail", this.$store.state.user.email);
      formData.set("tgtEmail", this.movementWalletEmail);
      formData.set("value", this.movementAmount);
      formData.set("tranfer", 1);
      formData.set("categoryName", this.movementCategoryName);
      formData.set("description", this.movementDescription);
      formData.set("source_description", this.movementSourceDescription);
      axios.post("api/movements/expense/transfer", formData).then(response => {
        //Todo send websocket thing
        //console.log(response.data);
        var userTarget = response.data.user_tgt;
        var userSource = response.data.user_src; //Me
        this.$socket.emit("sendMovement", userSource, userTarget);
        this.$socket.emit("sendMovement", userSource, userSource);
        this.getBalance();
      });
    }
  },
  mounted() {
    this.getBalance();
    this.setCategoryNames();
    this.getAllWalletEmails();
  },
  watch: {
    movementAmount: function() {
      this.overBudget = this.movementAmount > this.balance;
      this.balanceAfter = this.balance - this.movementAmount;
      this.balanceAfter = Math.round(this.balanceAfter * 100) / 100;
      if (this.movementAmount < 0.01) {
        this.movementAmount = 0.01;
      }
      if (this.movementAmount > 5000) {
        this.movementAmount = 5000;
      }
    },
    movementWalletEmail() {
      this.walletEmailExists = this.walletEmails.includes(
        this.movementWalletEmail
      );
    },
    movementMBEntityCode() {
      this.invalidMovementMBEntityCode =
        this.movementMBEntityCode < 10000 || this.movementMBEntityCode > 99999;
    },
    movementMBPaymentReference() {
      this.invalidMovementMBPaymentReference =
        this.movementMBPaymentReference < 100000000 ||
        this.movementMBPaymentReference > 999999999;
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
    },
    balance() {
      this.balance = Math.round(this.balance * 100) / 100;
    }
  },
  sockets: {}
};
</script>

<style>
</style>