<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    <div>
      <GChart
        type="LineChart"
        :data="lineChartMovementEndBalance.chartData"
        :options="lineChartMovementEndBalance.chartOptions"
      />
      <GChart
        type="PieChart"
        :data="pieChartIncomesAndExpenses.chartData"
        :options="pieChartIncomesAndExpenses.chartOptions"
      />
      <GChart
        type="PieChart"
        :data="pieChartTransfers.chartData"
        :options="pieChartTransfers.chartOptions"
      />
      <GChart
        type="PieChart"
        :data="barChartCategories.chartData"
        :options="barChartCategories.chartOptions"
      />
      <GChart
        type="BarChart"
        :data="barChartCategoriesExpensesValues.chartData"
        :options="barChartCategoriesExpensesValues.chartOptions"
      />
      <GChart
        type="BarChart"
        :data="barChartCategoriesIncomesValues.chartData"
        :options="barChartCategoriesIncomesValues.chartOptions"
      />
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
      categories: null,
      movementsCategoriesSum: null,
      pieChartIncomesAndExpenses: {
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
      },
      pieChartTransfers: {
        chartData: [],
        chartOptions: {
          chart: {
            title: "Company Performance"
          }
        }
      },
      lineChartMovementEndBalance: {
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
      barChartCategories: {
        chartData: [],
        chartOptions: {
          chart: {
            title: "Company Performance"
          }
        }
      },
      barChartCategoriesExpensesValues: {
        chartData: [],
        chartOptions: {
          chart: {
            title: "Company Performance"
          }
        }
      },
      barChartCategoriesIncomesValues: {
        chartData: [],
        chartOptions: {
          chart: {
            title: "Company Performance"
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
    updatePieChartIncomesAndExpenses(movements) {
      var countIncomes = movements.filter(mov => mov.type === "i").length;
      var countExpenses = movements.filter(mov => mov.type === "e").length;
      this.pieChartIncomesAndExpenses.chartData = [
        ["Name", "Count"],
        ["Incomes,", countIncomes],
        ["Expenses,", countExpenses]
      ];
    },
    getCategories: function(type) {
      axios.get("api/categories/names").then(response => {
        this.categories = response.data;
      });
    },
    updatePieChartTransfers(movements) {
      var countTransfers = movements.filter(mov => mov.transfer === 1).length;
      var countNonTransfers = movements.filter(mov => mov.transfer === 0)
        .length;
      this.pieChartTransfers.chartData = [
        ["Name", "Count"],
        ["Transfers", countTransfers],
        ["Non Transfers", countNonTransfers]
      ];
    },
    updateMovementEndBalanceLineChart(movements) {
      this.lineChartMovementEndBalance.chartData = [
        ["Date", "EndBalance"],
        [movements[0].date, parseFloat(movements[0].end_balance)]
      ];
      var i;
      for (i = 1; i < movements.length; i++) {
        this.lineChartMovementEndBalance.chartData.push([
          movements[i].date,
          parseFloat(movements[i].end_balance)
        ]);
      }
    },
    updateBarChartsCategories(movements) {
      this.barChartCategories.chartData = [["Category", "Movements"]];
      var i;
      for (i = 0; i < this.categories.length; i++) {
        this.barChartCategories.chartData.push([
          this.categories[i].name.toUpperCase(),
          this.countMovementsOfCategoryId(movements, this.categories[i].id)
        ]);
      }
    },
    countMovementsOfCategoryId(movements, catId) {
      var cnt = movements.filter(mov => mov.category_id === catId).length;
      return cnt;
    },
    updateBarChartsCategoriesExpensesValues(movements) {
      var expenses = this.movementsCategoriesSum.expenses;
      expenses.sort(this.compareCategoryValue);
      this.barChartCategoriesExpensesValues.chartData = [
        ["Expense", "Sum €"]
        ];
      var i;
      for (i = 0; i < expenses.length; i++) {
        this.barChartCategoriesExpensesValues.chartData.push([
          expenses[i].category,
          expenses[i].value
        ]);
      }
    },
    updateBarChartsCategoriesIncomesValues(movements) {
      var incomes = this.movementsCategoriesSum.incomes;
      incomes.sort(this.compareCategoryValue);
      this.barChartCategoriesIncomesValues.chartData = [
        ["Income", "Sum €"]
        ];
      var i;
      for (i = 0; i < incomes.length; i++) {
        this.barChartCategoriesIncomesValues.chartData.push([
          incomes[i].category,
          incomes[i].value
        ]);
      }
    },
    getSumOfCategoryValues(movements, categories) {
      var incomes = [];
      var expenses = [];
      var i;
      for (i = 0; i < categories.length; i++) {
        if (categories[i].type == "i") {
          incomes.push({
            category: categories[i].name.toUpperCase(),
            value: this.getSumOfMovementValues(
              movements.filter(mov => mov.category_id === categories[i].id)
            )
          });
        }
      }
      for (i = 0; i < categories.length; i++) {
        if (categories[i].type == "e") {
          expenses.push({
            category: categories[i].name.toUpperCase(),
            value: this.getSumOfMovementValues(
              movements.filter(mov => mov.category_id === categories[i].id)
            )
          });
        }
      }
      return { incomes: incomes, expenses: expenses };
    },
    getSumOfMovementValues(movementArray) {
      var i;
      var sum = 0;
      for (i = 0; i < movementArray.length; i++) {
        sum += parseFloat(movementArray[i].value);
      }
      sum = Math.round(sum * 100) / 100;
      return sum;
    },
    compareCategoryValue(a, b) {
      return a.value - b.value;
    }
  },
  mounted() {
    if (!this.$store.state.user) {
      this.$router.push({ path: "/login" });
    }
    if (this.$store.state.user.type != "u") {
      this.$router.push({ path: "/welcome" });
      return;
    }
    this.getAllUserMovements();
    this.getCategories();
  },
  watch: {
    movements() {
      this.movementsCategoriesSum = this.getSumOfCategoryValues(
        this.movements,
        this.categories
      );
      this.updatePieChartIncomesAndExpenses(this.movements);
      this.updatePieChartTransfers(this.movements);
      this.updateMovementEndBalanceLineChart(this.movements);
      this.updateBarChartsCategories(this.movements);
      this.updateBarChartsCategoriesExpensesValues(this.movements);
      this.updateBarChartsCategoriesIncomesValues(this.movements);
    }
  }
};
</script>

<style>
#btn {
  color: black;
}
</style>