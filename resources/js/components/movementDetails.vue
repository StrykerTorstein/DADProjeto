<template>
  <div class="jumbotron">
    <table class="table table-striped">
      <thead>
        <h2>
          <b>Movement {{movement.id}} Details</b>
        </h2>
        <div>
          <div>
            <div>
              <b>Description:</b>
            </div>
            <div>{{movement.description}}</div>
          </div>
        </div>
        <div>
          <div>
            <div>
              <b>Source Description:</b>
            </div>
            <div>{{movement.source_description}}</div>
          </div>
        </div>
        <div>
          <div>
            <div>
              <b>IBAN:</b>
            </div>
            <div>{{movement.iban}}</div>
          </div>
        </div>
        <div>
          <div>
            <div>
              <b>MB Entity Code:</b>
            </div>
            <div>{{movement.mb_entity_code}}</div>
          </div>
        </div>
        <div>
          <div>
            <div>
              <b>MB Payment Reference:</b>
            </div>
            <div>{{movement.mb_payment_reference}}</div>
          </div>
        </div>
        <div>
          <div v-if="movementPhoto">
            <b>Photo:</b>
          </div>
          <div>
            <v-img v-if="movementPhoto" height="256px" width="256px" :src="movementPhoto"></v-img>
          </div>
        </div>
        <div>
          <v-btn
            v-if="!editingMovement"
            class="btn btn-primary"
            v-on:click.prevent="editingMovement = true"
          >Edit</v-btn>
          <div v-if="editingMovement">
            Description
            <textarea
              type="text"
              class="form-control"
              v-model="movementDescription"
              name="movementDescription"
              id="inputmovementDescription"
              rows="4"
              cols="50"
            />
            <div v-if="movementCategories && movementCategories.length > 0" class="form-group">
              <label for="category_id">Category</label>
              <select class="form-control" id="name" name="name" v-model="movementCategory">
                <option v-for="item in movementCategories" :key="item.id">{{ item.name.toUpperCase() }}</option>
              </select>
            </div>
            <v-btn class="btn btn-danger" v-on:click.prevent="editingMovement = false">Cancel</v-btn>
            <v-btn class="btn btn-success" v-on:click.prevent="updateMovement()">Save</v-btn>
          </div>
        </div>
      </thead>
    </table>
  </div>
</template>

<script>
export default {
  props: ["movement"],
  data: function() {
    return {
      currentUser: null,
      movementPhoto: null,
      editingMovement: false,
      movementDescription: null,
      movementCategories: null,
      movementCategory: null
    };
  },
  methods: {
    getPhoto() {
      this.movementPhoto = null;
      if (this.movement.email) {
        axios
          .get("api/users/getphotobyemail/" + this.movement.email)
          .then(response => {
            this.movementPhoto = response.data;
            this.movementPhoto = "storage/fotos/" + this.movementPhoto;
          })
          .catch(error => {
            this.movementPhoto = null;
          });
      }
    },
    updateMovement() {
      var currentMovement = this.movement;
      if (!currentMovement) {
        this.editingMovement = false;
        this.toastedError("Select a movement to edit");
        return;
      }
      axios.put('api/movements/updateMovementDescriptionAndCategory/'+currentMovement.id,{
        description: this.movementDescription, category: this.movementCategory
      }).then(response => {
        this.$emit('edited-movement');
        this.editingMovement = false;
      });
    },
    toastedError(msg) {
      this.$toasted.show(msg, {
        type: "error"
      });
    },
    getCategories(movement) {
      axios.get("api/categories/names").then(response => {
        var categories = response.data;
        this.movementCategories = categories.filter(
          cat => cat.type === this.movement.type
        );
        this.movementCategory = movement.category.toUpperCase();
      });
    }
  },
  mounted() {
    this.$data.user = this.$store.state.user;
    this.getPhoto();
  },
  watch: {
    movement: function() {
      if(!this.movement){
        return;
      }
      this.getPhoto();
      this.editingMovement = false;
    },
    editingMovement() {
      if (this.movement) {
        this.movementDescription = this.movement.description;
        this.getCategories(this.movement);
      }
    }
  }
};
</script>

<style>
</style>