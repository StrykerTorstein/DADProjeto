<template>
  <div>
    <h4>Virtual Wallet Chat</h4>
    <div>Write Message
      <input
        type="text"
        class="msgInputs"
        v-model="msgText"
        @keypress.enter="sendMsgText"
      >
      <!--
      <input
        type="checkbox"
        v-model="toDepartment"
        @keypress.enter="sendMsgText"
      >Department Only-->
    </div>
    <div>
      Messages:
      <textarea
        rows="4"
        class="msgInputs"
        v-model="allMsgText"
      >Global Chat</textarea>
    </div>
    <hr>
  </div>
</template>

<script type="text/javascript">
export default {
  data: function () {
    return {
      msgText: "",
      allMsgText: "",
      toDepartment: false,
    }
  },
  methods: {
    sendMsgText () {
      let msg = 'Anon';
      if(this.$store.state.user){
        msg = this.$store.state.user.name;
      }
      msg+= ': '+this.msgText;
      if(this.toDepartment){
        msg = "[DEPARTMENT] "+msg;
        this.$socket.emit('chat-dep',msg,this.$store.state.user);
      }else{
        this.$socket.emit('chat',msg);
      }
      this.allMsgText = msg + "\n" + this.allMsgText;
      this.msgText = "";
      //Look in WebServer/server.js
    }
  },
  sockets:{
    //Here we handle the messages we RECEIVE
    chat: function(msg){
      this.allMsgText = msg + "\n" + this.allMsgText;
    }
  }
}
</script>

<style scoped>
.msgInputs {
  width: 100%;
}
</style>