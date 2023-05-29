let timeoutHander = null

let init = () => {
    if (!window['swoole-cli-ws']) {
        let wsServer = 'ws://' + document.domain + ':9502/websocket';
        let websocket = new WebSocket(wsServer);
        websocket.onopen = function (evt) {
            console.log("Connected to WebSocket server.");
            websocket.send('hello');
            window['swoole-cli-ws'] = websocket;
        };
        websocket.onclose = function (evt) {
            console.log("Disconnected");
            window['swoole-cli-ws'] = null;
        };

        websocket.onmessage = function (evt) {
            console.log('Retrieved data from server: ' + evt.data);
        };

        websocket.onerror = function (evt, e) {
            console.log('Error occured: ' + evt.data);
            window['swoole-cli-ws'] = null;
        };
        if (!window['swoole-cli-ws']) {
            timeoutHander = setTimeout(init, 3000)
        }
    } else {
        clearTimeout(timeoutHander)
    }
}
let send = (message) => {
    if (window['swoole-cli-ws']) {
        let ws = window['swoole-cli-ws'];
        ws.send(message)
    } else {
        init()
    }

}


export {init, send}

