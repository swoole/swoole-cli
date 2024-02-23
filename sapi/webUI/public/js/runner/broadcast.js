let pc
let sendChannel
let receiveChannel

const signaling = new BroadcastChannel('webrtc')
signaling.onmessage = e => {
    switch (e.data.type) {
        case 'offer':
            handleOffer(e.data)
            break
        case 'answer':
            handleAnswer(e.data)
            break
        case 'candidate':
            handleCandidate(e.data)
            break
        case 'ready':
            // A second tab joined. This tab will enable the start button unless in a call already.
            if (pc) {
                console.log('already in call, ignoring')
                return
            }
            break
        case 'bye':
            if (pc) {
                hangup()
            }
            break
        default:
            console.log('unhandled', e)
            break
    }
}
signaling.postMessage({type: 'ready'})

const state_box = {
    start: false,
    close: false,
    send: false
}
const init = async () => {
    await createPeerConnection()
    sendChannel = pc.createDataChannel('sendDataChannel')
    sendChannel.onopen = onSendChannelStateChange
    sendChannel.onmessage = onSendChannelMessageCallback
    sendChannel.onclose = onSendChannelStateChange

    const offer = await pc.createOffer()
    signaling.postMessage({type: 'offer', sdp: offer.sdp})
    await pc.setLocalDescription(offer)
}

const close = async () => {
    signaling.postMessage({type: 'bye'})
}

async function hangup() {
    if (pc) {
        pc.close()
        pc = null
    }
    sendChannel = null
    receiveChannel = null
    console.log('Closed peer connections')

}

function createPeerConnection() {
    pc = new RTCPeerConnection()
    pc.onicecandidate = e => {
        const message = {
            type: 'candidate',
            candidate: null
        }
        if (e.candidate) {
            message.candidate = e.candidate.candidate
            message.sdpMid = e.candidate.sdpMid
            message.sdpMLineIndex = e.candidate.sdpMLineIndex
        }
        signaling.postMessage(message)
    }
}

async function handleOffer(offer) {
    if (pc) {
        console.log('existing peerconnection')
        return
    }
    await createPeerConnection()
    pc.ondatachannel = receiveChannelCallback
    await pc.setRemoteDescription(offer)

    const answer = await pc.createAnswer()
    signaling.postMessage({type: 'answer', sdp: answer.sdp})
    await pc.setLocalDescription(answer)
}

async function handleAnswer(answer) {
    if (!pc) {
        console.log('no peerconnection')
        return
    }
    await pc.setRemoteDescription(answer)
}

async function handleCandidate(candidate) {
    if (!pc) {
        console.log('no peerconnection')
        return
    }
    if (!candidate.candidate) {
        await pc.addIceCandidate(null)
    } else {
        await pc.addIceCandidate(candidate)
    }
}

function sendData() {
    const data = dataChannelSend.value
    if (sendChannel) {
        sendChannel.send(data)
    } else {
        receiveChannel.send(data)
    }
    console.log('Sent Data: ' + data)
}

function receiveChannelCallback(event) {
    console.log('Receive Channel Callback')
    receiveChannel = event.channel
    receiveChannel.onmessage = onReceiveChannelMessageCallback
    receiveChannel.onopen = onReceiveChannelStateChange
    receiveChannel.onclose = onReceiveChannelStateChange
}

function onReceiveChannelMessageCallback(event) {
    console.log('onReceiveChannel Received Message')
    console.log(event.data)
}

function onSendChannelMessageCallback(event) {
    console.log('onSendChannel Received Message')
    console.log(event.data)
}

function onSendChannelStateChange() {
    const readyState = sendChannel.readyState
    console.log('Send channel state is: ' + readyState)
    if (readyState === 'open') {

    } else {

    }
}

function onReceiveChannelStateChange() {
    const readyState = receiveChannel.readyState
    console.log(`Receive channel state is: ${readyState}`)
    if (readyState === 'open') {

    } else {

    }
}

let broadcast = () => {
    init()
}
export default broadcast
