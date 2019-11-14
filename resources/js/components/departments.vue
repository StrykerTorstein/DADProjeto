<template>
  <div>
    <div class="jumbotron">
      <h1>{{ title }}</h1>
    </div>
    
    <!-- here we map the variable users to the 'props' users on userList.vue -->
    <!-- @ is the same as v-on: -->
    <department-list v-bind:departments="departments"></department-list>
  </div>
</template>

<script>

import DepartmentList from './departmentList';

export default {
  data: () => {
    return {
      title: "List Departments",
      departments: []
    };
  },
  methods: {
    getDepartments: function() {
      axios.get("api/departments").then(response => {
        this.departments = response.data.data;
      });
    }
  },
  mounted() {
    this.getDepartments();
  },
  components:{
      'department-list' : DepartmentList
  }
};
</script>

<style>
</style>