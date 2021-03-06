/*jshint esversion: 6 */

var app = require('http').createServer();

// Se tiverem problemas com "same-origin policy" deverão activar o CORS.

// Aqui, temos um exemplo de código que ativa o CORS (alterar o url base) 

// var app = require('http').createServer(function(req,res){
// Set CORS headers
//  res.setHeader('Access-Control-Allow-Origin', 'http://---your-base-url---');
//  res.setHeader('Access-Control-Request-Method', '*');
//  res.setHeader('Access-Control-Allow-Methods', 'UPGRADE, OPTIONS, GET');
//  res.setHeader('Access-Control-Allow-Credentials', true);
//  res.setHeader('Access-Control-Allow-Headers', req.header.origin);
//  if ( req.method === 'OPTIONS' || req.method === 'UPGRADE' ) {
//      res.writeHead(200);
//      res.end();
//      return;
//  }
// });

// NOTA: A solução correta depende da configuração do próprio servidor, 
// e alguns casos do próprio browser.
// Assim sendo, não se garante que a solução anterior funcione.
// Caso não funcione é necessário procurar/investigar soluções alternativas

var io = require('socket.io')(app);

var LoggedUsers = require('./loggedusers.js');

app.listen(8080, function(){
    console.log('listening on *:8080');
});

// ------------------------
// Estrutura dados - server
// ------------------------

// loggedUsers = the list (map) of logged users. 
// Each list element has the information about the user and the socket id
// Check loggedusers.js file

let loggedUsers = new LoggedUsers();

io.on('connection', function (socket) {
    console.log('client has connected (socket ID = '+socket.id+')' );

    socket.on('sendMovement',(sourceUser, destUser)=>{
        let userInfo = loggedUsers.userInfoByID(destUser.id);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id === null) {
            //socket.emit("privateMessage_unavailable", destUser);
        } else {
            io.to(socket_id).emit("movementReceived",destUser);
            socket.emit("movementSent",sourceUser);
        }
    });

    socket.on('chat',(msg)=>{
        //Note this does NOT call this function (socket.on('chat)...)
        //This calls the function on the sockets: { ... chat ... } 
        // on the target component that uses 'chat' socket method
        socket.broadcast.emit('chat',msg);
    });

    socket.on('chat-dep',(msg,user)=>{
        if(user){
            socket.to(`department_${user.department_id}`).emit('chat',msg);
        }
    });

    socket.on('login',(user)=>{
        socket.join(`department_${user.department_id}`);
        loggedUsers.addUserInfo(user, socket.id);
    });

    socket.on('logout',(user)=>{
        socket.leave(`department_${user.department_id}`);
        loggedUsers.removeUserInfoByID(user.id);
    });

    socket.on("privateMessage", function(msg, sourceUser, destUser) {
        let userInfo = loggedUsers.userInfoByID(destUser.id);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id === null) {
            socket.emit("privateMessage_unavailable", destUser);
        } else {
            io.to(socket_id).emit("privateMessage", msg, sourceUser);
            socket.emit("privateMessage_sent", msg, destUser);
        }
    });

    // Emit message to the same cliente 
    //socket.emit('my_active_games_changed');

    // Handle message sent from the client to the server
    // socket.on('messageType_from_client_to_server', function (data){

    // });

});
