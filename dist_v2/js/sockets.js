const socket = new WebSocket('ws://localhost:8081');
socket.onopen = function(event) {
    console.log('Connected to the server');
    // Ejemplo de uso
    subscribeToChannels('project1', 'admin', 'user123');
    publishToChannels('project1', 'admin', 'user123', 'Hello everyone!');
};