import broadcast from "./broadcast.js";

const app = () => {
    const URLObj = new URL(location.href)

    const wsServer = 'ws://' + URLObj.host + '/websocket'
    console.log(URLObj, wsServer)

    const websocket = new WebSocket(wsServer)
    websocket.onopen = function (event) {
        console.log('Connected to WebSocket server.')

        websocket.send(JSON.stringify({
            action: 'get_instance_state'
        }))
        broadcast()
    }

    websocket.onclose = function (evt) {
        console.log('Disconnected')
    }

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data)
    }

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data)
    }
    console.log(websocket)

    /*
      websocket.on('pong',()=>{
          console.log("received pong");
      });

       */
}
export default app
