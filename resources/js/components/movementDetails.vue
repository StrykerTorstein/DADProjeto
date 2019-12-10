<template>
<div class="jumbotron" >
  <table class="table table-striped">
      <thead>
      <h2><b>Movement Details</b></h2>
	    <div>
            <div> 
                <div> <b>Description: </b></div>
                <div> {{movement.description}} </div>
            </div>
        </div>
        <div>
            <div> 
                <div> <b>Source Description: </b></div>
                <div> {{movement.source_description}} </div>
            </div>
        </div>
        <div>
            <div>  
                <div> <b>IBAN: </b></div>
                <div> {{movement.iban}} </div>
            </div>
        </div>
        <div>
            <div> 
                <div> <b>MB Entity Code: </b></div>
                <div> {{movement.mb_entity_code}} </div>
            </div>
        </div>
        <div>
            <div> 
                <div> <b>MB Payment Reference:</b> </div>
                <div> {{movement.mb_payment_reference}} </div>
            </div>
        </div>
        <div> 
                <div> <b>Photo: </b></div>
                <div>
        <v-img
          v-if="movementPhoto"
          height="256px"
          width="256px"
          :src="movementPhoto"
        >
        </v-img></div>
        </div>
    </thead>
  </table>
</div>
</template>

<script>
	export default{
		props:['movement'],
		data: function(){
			return {
                currentUser: null,
                movements: null,
                movementPhoto: null
            };
        },
        methods: {
            getMovements: function() {
                axios.get("api/"+ this.$data.user.id +"/movements").then(response => {
                    this.movements = response.data.data;
                });
            },
            getPhoto(){
                axios.get("api/users/getphotobyemail/"+this.movement.email).then(response => {
                    this.movementPhoto = response.data;
                    this.movementPhoto = "storage/fotos/" + this.movementPhoto;
                });
            }
        },
        mounted(){
            this.$store.commit("loadTokenAndUserFromSession");
            this.$data.user = this.$store.state.user;
            this.getMovements();
            this.getPhoto();
        }
	};
</script>

<style>
	
</style>