var app = require('express')();
var http = require('http').createServer(app);
var bodyParser = require('body-parser')
var io = require('socket.io')(http);

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

app.use(bodyParser.json());

io.on('connect', function (socket) {
   socket.on('join', function (data) {
    socket.join(data);
    console.log(data)
  });
  socket.on('leave', function (data) {
    socket.leave(data);
    console.log(data)
  });

  app.post('/counting', function(request, response){
      console.log(request.body);      // your JSON
      io.to(request.body['id_camera']).emit('counting_live', request.body);
      response.send(request.body);    // echo the result back
  });

});

http.listen(3000, function(){
  console.log('listening on *:3000');
});
