<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div>
      Line
      <GChart type="LineChart" :data="lineChart.chartData" :options="lineChart.chartOptions" />
    </div>
    <div>
      Pie
      <GChart type="PieChart" :data="pieChart.chartData" :options="pieChart.chartOptions" />
    </div>
  </div>
</template>

<script>
import { Line } from "vue-chartjs";
export default {
  extends: Line,
  data: () => {
    return {
      title: "Movement Statistics",
      movements: null,
      // Array will be automatically processed with visualization.arrayToDataTable function
      lineChart: {
        chartData: [
          ["Year", "Stonks"],
          ["2014", 15],
          ["2015", 350],
          ["2016", 500],
          ["2017", 1500]
        ],
        chartOptions: {
          chart: {
            title: "Company Performance",
            curveType: "function",
            legend: { position: "bottom" }
          }
        }
      },
      pieChart: {
        chartData: [
          ["Year", "Sales", "Expenses", "Profit"],
          ["2014", 1000, 400, 200],
          ["2015", 1170, 460, 250],
          ["2016", 660, 1120, 300],
          ["2017", 1030, 540, 350]
        ],
        chartOptions: {
          chart: {
            title: "Company Performance",
            curveType: "function",
            legend: { position: "bottom" }
          }
        }
      }
    };
  },
  methods: {
    getAllUserMovements() {
      axios
        .get("api/movements/getAllUserMovements/", {
          params: this.$store.state.user
        })
        .then(response => {
          this.movements = response.data;
        });
    },
    updatePieChart(incomes,expenses){
      this.pieChart.chartData = null;
      this.pieChart.chartData = [
        ["Name","Count"],
        ["Incomes,",incomes],
        ["Expenses,",expenses]
      ]
    }
  },
  mounted() {
    if(!this.$store.state.user){
      this.$router.push({ path: "/login" });
    }
    if (this.$store.state.user.type != "u") {
      this.$router.push({ path: "/welcome" });
      return;
    }
    this.getAllUserMovements();
  },
  watch:{
    movements(){
      var countIncomes = this.movements.filter((mov) => mov.type === 'i').length;
      var countExpenses = this.movements.filter((mov) => mov.type === 'e').length;
      this.updatePieChart(countIncomes,countExpenses);
      console.log(`Expenses: ${countExpenses} Incomes: ${countIncomes}`);
    }
  }
};
</script>

<style>
#btn {
  color: black;
}
</style>