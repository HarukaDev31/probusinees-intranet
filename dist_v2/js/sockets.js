const socket = new WebSocket('wss://websockets.probusiness.pe');
// Suscribirse a múltiples canales
function subscribeToChannels(project, role, user) {
    const message = JSON.stringify({
        action: 'subscribe',
        project: project,
        role: role,
        user: user
    });
    socket.send(message);
}

// Publicar un mensaje en múltiples canales
function publishToChannels(project, role, user, message) {
    const msg = JSON.stringify({
        action: 'publish',
        project: project,
        role: role,
        user: user,
        message: message
    });
    socket.send(msg);
}

// Manejar mensajes recibidos del servidor
socket.onmessage = function(event) {
  // console.log(event);
  //   const message = event.data;
  //   console.log('Received message:', message);
  //   // Aquí puedes manejar los mensajes recibidos del servidor
  //   try{
  //     alert(message);
  //   }
  //   catch(e){
  //     console.log(e);
  //   }

};
socket.onopen = function(event) {
    console.log('Socket connected');
    subscribeToChannels('project', 'role', 'user');
};