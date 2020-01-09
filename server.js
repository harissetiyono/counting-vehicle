var app = require('express')();
var http = require('http').createServer(app);
var bodyParser = require('body-parser')
var io = require('socket.io')(http);
var cors = require('cors');

// bodyParser = {
  // json: {limit: '50mb', extended: true},
  // urlencoded: {limit: '50mb', extended: true },
//   cors : {origin: '*'}
// };

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

app.use( cors({origin: '*'}), bodyParser.json({limit: '50mb', extended: true}));

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

  app.post('/face_recognition', function(request, response){
    console.log(request.body);      // your JSON
    io.to(request.body['id_camera']).emit('face_detect', request.body);
    response.send(request.body);    // echo the result back
  });

  app.post('/find_face', function(request, response){
    console.log(request.body);      // your JSON
    io.emit('find_face', request.body);
    response.send(request.body);    // echo the result back
  });

});

app.post('/plate_recognition', function(request, response){
  console.log(request.body);      // your JSON
  io.emit('plate_recognition', request.body);
  response.send(request.body);    // echo the result back
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});
