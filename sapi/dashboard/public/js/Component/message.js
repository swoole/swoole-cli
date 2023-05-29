
var wsServer = 'ws://'+document.domain+':9502/websocket';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
    websocket.send('hello');
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};


