<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="department in departments" :key="department.id">
        <td>{{ department.name }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
    props:['departments'],
    data:()=>{
        return{
            
        }
    },methods:{
        
    }
};
</script>

<style scoped>
</style>