var app = require('express')();
var http = require('http').createServer(app);
var bodyParser = require('body-parser')
var io = require('socket.io')(http);

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

app.use(bodyParser.json());

io.on('connection', function (socket) {
   socket.on('join', function (data) {
    socket.join(data);
    console.log(data)
  });

   app.post('/alpr', function(request, response){
      console.log(request.body);      // your JSON
      io.emit('alpr', request.body);
      response.send(request.body);    // echo the result back
   });

});

http.listen(3000, function(){
  console.log('listening on *:3000');
});
