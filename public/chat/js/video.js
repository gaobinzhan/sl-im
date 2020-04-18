import {output, getQueryValue} from "./util.js";
import {friend_video_subscribe, friend_video_publish} from "./api.js";

const stunServer = parent.layui.jquery(".stunServer").val();
const localVideo = document.getElementById('localVideo');
const remoteVideo = document.getElementById('remoteVideo');
const configuration = {
  iceServers: [{
    urls: stunServer
  }]
};
var Video;
var loadingIndex;
let subject = getQueryValue('room_id');
let answer = 0;
let pc, localStream;

function getMediaStream(stream) {
  localVideo.srcObject = localStream;
  localStream = stream;
}

function createMessage(cmd, data = {}, ext = {}) {
  let msg = {
    cmd: cmd,
    data: data,
    ext: ext
  };
  output(msg);
  return JSON.stringify(msg);
}

function setLoadingIndex(index) {
  loadingIndex = index;
}

function socketEvent(webSocket) {
  webSocket.onopen = function (event) {
    wsOpen(event);
  };
  webSocket.onmessage = function (event) {
    wsReceive(event);
  };
}

function wsSend(data) {
  Video.send(data)
}

function wsReceive(e) {
  let result = JSON.parse(e.data);
  let data = result.data;
  switch (result.event) {
    case 'accept':
      layui.layer.close(loadingIndex);
      break;
    case 'call':
      icecandidate(localStream);
      pc.createOffer({
        offerToReceiveAudio: 1,
        offerToReceiveVideo: 1
      }).then(function (desc) {
        pc.setLocalDescription(desc).then(
          function () {
            wsSend(createMessage(friend_video_publish, {subject: subject, event: 'offer', data: pc.localDescription}));
          }
        ).catch(function (e) {
          alert(e);
        });
      }).catch(function (e) {
        alert(e);
      });
      break;
    case 'answer':
      pc.setRemoteDescription(new RTCSessionDescription(data), function () {
      }, function (e) {
        alert(e);
      });
      break;
    case 'offer':
      icecandidate(localStream);
      pc.setRemoteDescription(new RTCSessionDescription(data), function () {
        if (!answer) {
          pc.createAnswer(function (desc) {
              pc.setLocalDescription(desc, function () {
                wsSend(createMessage(friend_video_publish, {
                  subject: subject,
                  event: 'answer',
                  data: pc.localDescription
                }))
              }, function (e) {
                alert(e);
              });
            }
            , function (e) {
              alert(e);
            });
          answer = 1;
        }
      }, function (e) {
        alert(e);
      });
      break;
    case 'candidate':
      pc.addIceCandidate(new RTCIceCandidate(data), function () {
      }, function (e) {
        alert(e);
      });
      break;
  }
}

function wsOpen() {
  wsSend(createMessage(friend_video_subscribe, {subject: subject}));

  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    console.error('the getUserMedia is not supported!');
    return false;
  }
  navigator.mediaDevices.getUserMedia({
    audio: true,
    video: true
  }).then(function (stream) {
    if (localStream) {
      stream.getAudioTracks().forEach((track) => {
        localStream.addTrack(track);
        stream.removeTrack(track);
      });
    } else {
      localStream = stream;
    }
    localVideo.srcObject = localStream;
    wsSend(createMessage(friend_video_publish, {subject: subject, event: 'call', data: null}));
  }).catch(function (e) {
    console.error('Failed to get Media Stream!', e);
  });
}

function createVideoSocket(webRtcUrl, protocols) {
  output(webRtcUrl, 'WebRtc');
  Video = new WebSocket(webRtcUrl, protocols);
  return Video;
};


function leave() {
  pc.close();
}

function icecandidate(localStream) {
  pc = new RTCPeerConnection(configuration);
  pc.onicecandidate = function (event) {
    if (event.candidate) {
      wsSend(createMessage(friend_video_publish, {subject: subject, event: 'candidate', data: event.candidate}));
    }
  };
  try {
    pc.addStream(localStream);
  } catch (e) {
    let tracks = localStream.getTracks();
    for (let i = 0; i < tracks.length; i++) {
      pc.addTrack(tracks[i], localStream);
    }
  }
  pc.onaddstream = function (e) {
    remoteVideo.srcObject = e.stream;
  };
}


export {
  createVideoSocket,
  socketEvent,
  setLoadingIndex
}
