<template>
    <div>
        <div>
    <h3 class="text-center">{{ title }}</h3>
    <br>
    <h2>Current Player : {{ currentPlayer }}</h2>
    <br>
    </div>
    <div class="game-zone-content">       
        <div class="alert alert-success" v-if="showSuccess">
            <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
            <strong>{{ successMessage }} &nbsp;&nbsp;&nbsp;&nbsp;<a v-show="gameEnded" v-on:click.prevent="restartGame">Restart</a></strong>
        </div>

        <div class="board">
            <div v-for="(piece, key) of board" :key="key">
                <img v-bind:src="pieceImageURL(piece)" v-on:click="clickPiece(key)">
            </div>
        </div>
        <hr>
    </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return{
            title: 'TicTacToe',
            showSuccess: false,
            showFailure: false,
            successMessage: '',
            failMessage: '',
            currentValue: 1,
            gameEnded:false,
            board: [0,0,0,0,0,0,0,0,0],
            player1: undefined,
            player2: undefined
        };
    },
    methods: {
        pieceImageURL: function (piece) {
            var imgSrc = String(piece);
            return 'img/' + imgSrc + '.png';
        },
        clickPiece: function(index) {
            if(this.board[index] || this.gameEnded) return;
            this.board[index] = this.currentValue;
            this.successMessage = 'Player '+this.currentPlayer+' has Played';
            this.showSuccess = true;
            this.currentValue = (this.currentValue == 1)? 2 : 1;
            this.checkGameEnded();
        },
        restartGame:function(){
            console.log('restartGame');
            this.board= [0,0,0,0,0,0,0,0,0];
            this.showSuccess= false;
            this.showFailure= false;
            this.successMessage= '';
            this.failMessage= '';
            this.currentValue= 1;
            this.gameEnded= false;
        },
        // ----------------------------------------------------------------------------------------
        // GAME LOGIC - START
        // ----------------------------------------------------------------------------------------
        hasRow: function(value){
            return  ((this.board[0]==value) && (this.board[1]==value) && (this.board[2]==value)) || 
            ((this.board[3]==value) && (this.board[4]==value) && (this.board[5]==value)) || 
            ((this.board[6]==value) && (this.board[7]==value) && (this.board[8]==value)) || 
            ((this.board[0]==value) && (this.board[3]==value) && (this.board[6]==value)) || 
            ((this.board[1]==value) && (this.board[4]==value) && (this.board[7]==value)) || 
            ((this.board[2]==value) && (this.board[5]==value) && (this.board[8]==value)) || 
            ((this.board[0]==value) && (this.board[4]==value) && (this.board[8]==value)) || 
            ((this.board[2]==value) && (this.board[4]==value) && (this.board[6]==value));
        },
        checkGameEnded: function(){
            if (this.hasRow(1)) {
                this.successMessage = this.player1.name+' won the Game';
                this.showSuccess = true;
                this.gameEnded = true;
            }
            if (this.hasRow(2)) {
                this.successMessage = this.player2.name+' won the Game';
                this.showSuccess = true;
                this.gameEnded = true;
            }
            if (this.isBoardComplete()) {
                this.successMessage = 'The Game ended in a Tie';
                this.showSuccess = true;
                this.gameEnded = true;
            }
            return false;
        },
        isBoardComplete:function(){
            var returnValue = true;
            this.board.forEach(function(element) {
                if (element === 0) {
                    returnValue = false;
                    return;
                }
            });
            return returnValue;
        }
        // ----------------------------------------------------------------------------------------
        // GAME LOGIC - END
        // ----------------------------------------------------------------------------------------        
    },
    computed:{
        //Whenever CurrentPlayer changes, this function is called.
        currentPlayer: function(){ 
            switch(this.currentValue){
                case 1: return this.player1.name;
                case 2: return this.player2.name;
            }
        }
    },
    created(){
        this.player1 = this.$root.player1 || {name : 'Player 1'};
        this.player2 = this.$root.player2 || {name : 'Player 2'};
    }
}
</script>