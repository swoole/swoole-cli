let app=()=>{
    let URLObj = new URL(location.href);

    let  wsServer = 'ws://'+URLObj['host'] +'/websocket';
    console.log(URLObj,wsServer)

    let  websocket = new WebSocket(wsServer);
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
      /*
        setInterval(() => {
            websocket.ping();
        }, 10*1000);
        */

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
    console.log(websocket)

    /*
    websocket.on('pong',()=>{
        console.log("received pong");
    });

     */
}
export default app
