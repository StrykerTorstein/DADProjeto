<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
      <h2>{{counterText}}</h2>
    </div>
    <div v-if="allUsers && movementStatistics">
      <h2>Statistics</h2>
      <GChart
        type="LineChart"
        :data="lineChartUsersOverTime.chartData"
        :options="lineChartUsersOverTime.chartOptions"
      />
      <GChart
        type="PieChart"
        :data="pieChartMovementCategories.chartData"
        :options="pieChartMovementCategories.chartOptions"
      />
      <div>
        <h3>Total Movements: {{ movementStatistics.total }}</h3>
        <h3>------------------------------------------------</h3>
        <h3>Expenses: {{ movementStatistics.totalExpenses }}%</h3>
        <h3>Incomes: {{ movementStatistics.totalTransfers }}%</h3>
        <h3>Transfers: {{ movementStatistics.totalTransfers }}%</h3>
        <h3>NonTransfers: {{ movementStatistics.totalNonTransfers }}%</h3>
        <h3>------------------------------------------------</h3>
        <h3>Smallest Movement: {{ movementStatistics.smallestMovement }} €</h3>
        <h3>Largest Movement: {{ movementStatistics.largestMovement }} €</h3>
        <h3>Average Movement: {{ movementStatistics.averageMovementValue }} €</h3>
      </div>
    </div>
  </div>
</template>

<script>
import { Line } from "vue-chartjs";
export default {
  extends: Line,
  data: () => {
    return {
      title: "Platform Statistics",
      movementStatistics: null,
      allUsers: null, //Ordered by date of creation (by the controller)
      adminUsers: [],
      operatorUsers: [],
      normalUsers: [],
      activeUsers: [],
      inactiveUsers: [],
      counterText: null,
      lineChartUsersOverTime: {
        chartData: [],
        chartOptions: {
          title: "Users over time",
          curveType: "function",
          legend: { position: "bottom" }
        }
      },
      pieChartMovementCategories: {
        chartData: [],
        chartOptions: {
          title: "Categories of all movements",
          legend: { position: "bottom" },
          height: 700,
          width: 700
        }
      }
    };
  },
  methods: {
    getAllUsers() {
      axios.get("api/users/all").then(response => {
        this.allUsers = response.data;
        this.adminUsers = this.allUsers.filter(user => user.type === "a");
        this.operatorUsers = this.allUsers.filter(user => user.type === "o");
        this.normalUsers = this.allUsers.filter(user => user.type === "u");
        this.activeUsers = this.allUsers.filter(user => user.active === 1);
        this.inactiveUsers = this.allUsers.filter(user => user.active === 0);
        this.counterText = `Users:${this.allUsers.length} `;
        this.counterText += ` Admins:${this.adminUsers.length} `;
        this.counterText += ` Operators:${this.operatorUsers.length} `;
        this.counterText += ` Regulars:${this.normalUsers.length} `;
        this.counterText += ` Active:${this.activeUsers.length} `;
        this.counterText += ` Inactive:${this.inactiveUsers.length} `;
      });
    },
    updateChartUsersOverTime(users) {
      var arrayDateCount = []; //Set as [dateOfUsers,'count']
      var i;
      for (i = 0; i < users.length; i++) {
        var date = Date.parse(users[i].created_at);
        var year = this.getDateYear(date);
        arrayDateCount.push({ year: year, users: i + 1 });
      }
      //console.log(arrayDateCount);
      this.lineChartUsersOverTime.chartData = [["Year", "Users"]];
      for (i = 0; i < arrayDateCount.length; i++) {
        this.lineChartUsersOverTime.chartData.push([
          arrayDateCount[i].year,
          arrayDateCount[i].users
        ]);
      }
    },
    getDateFormatted(date) {
      var d = new Date(date);
      var datestring =
        d.getDate() +
        "-" +
        (d.getMonth() + 1) +
        "-" +
        d.getFullYear() +
        " " +
        d.getHours() +
        ":" +
        d.getMinutes();
      return datestring;
    },
    getDateYear(date) {
      var d = new Date(date);
      var datestring = d.getFullYear();
      return parseInt(datestring);
    },
    getMovementStatistics() {
      axios.get("api/movements/movementStatistics").then(response => {
        this.movementStatistics = response.data;
        //Update statistics info charts
        this.pieChartMovementCategories.chartData = [
          ["Category", "Total"]
        ];
        var i;
        for (
          i = 0;
          i < this.movementStatistics.totalByCategory.length;
          i++
        ) {
          this.pieChartMovementCategories.chartData.push([
            this.movementStatistics.totalByCategory[i].category.toUpperCase(),
            this.movementStatistics.totalByCategory[i].total
          ]);
        }
      });
    }
  },
  mounted() {
    if (!this.$store.state.user) {
      this.$router.push({ path: "/welcome" });
    }
    if (this.$store.state.user && this.$store.state.user.type != "a") {
      this.$router.push({ path: "/welcome" });
      return;
    }
    this.getAllUsers();
    this.getMovementStatistics();
  },
  watch: {
    allUsers() {
      this.updateChartUsersOverTime(this.allUsers);
    }
  }
};
</script>

<style>
#btn {
  color: black;
}
</style>