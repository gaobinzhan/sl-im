import {output, isEmpty, getCookie} from "./util.js";
import {MessageActive} from './event.js';
import {user_ping, system_error, system_event} from "./api.js";

var Socket;
var heartbeat;
var messageList = {};

function createSocketConnection(url, protocols) {
  output(url, 'createSocketConnection');
  Socket = new WebSocket(url, protocols);
  return Socket;
}

function createMessage(cmd, data = {}, ext = {}) {
  let msg = {
    cmd: cmd,
    data: data,
    ext: ext
  };
  output(msg);
  if (cmd !== user_ping) {
    ack(msg);
  }
  return JSON.stringify(msg);
}

function ack(msg) {
  let data = msg.data;
  let message_id = data.message_id;
  messageList[message_id] = {
    msg: msg,
    timer: setTimeout(function () {
      if (!isEmpty(data.content)) {
        layui.layer.msg('消息发送失败：' + data.content, {
          time: 0
          , btn: ['重试', '取消']
          , yes: function (index) {
            Socket.send(JSON.stringify(msg));
            ack(messageList[message_id].msg);
            layui.layer.close(index);
          },
          btn2: function (index) {
            delete messageList[message_id];
            layui.layer.close(index);
          }
        });
      }
    }, 5000)
  };
  output(messageList);
}


function wsOpen(event) {
  output(event, 'onOpen');
  heartbeat = setInterval(function () {
    wsSend(createMessage(user_ping));
  }, 10000)
}

function wsReceive(event) {
  let result = eval('(' + event.data + ')');
  output(result, 'onMessage');
  if (layui.jquery.isEmptyObject(result)) {
    return false;
  }
  if (result.cmd && result.cmd === system_error) {
    layer.msg(result.cmd + ' : ' + result.msg);
    clearMessageListTimer(result);
    return false;
  }

  if (result.cmd && result.cmd === system_event) {
    let method = result.method;
    MessageActive[method] ? MessageActive[method](result.data) : '';
    return false;
  }


  if (result.cmd && result.cmd === user_ping) {
    return false;
  }

  clearMessageListTimer(result);

}

function clearMessageListTimer(result) {
  let message_id = result.data.message_id ?? '';
  if (message_id === '') return false;
  clearInterval(messageList[message_id].timer);
  delete messageList[message_id];
}

function wsError(event) {
  output(event, 'onError');
  clearInterval(heartbeat);
  reloadSocket();
}

function wsClose(event) {
  output(event, 'onClose');
  clearInterval(heartbeat);
  reloadSocket()
}

function reloadSocket() {
  layui.layer.msg(event.reason, {
    time: 0
    ,title:'连接异常关闭'
    , btn: ['重试', '取消']
    , yes: function (index) {
      var wsUrl = layui.jquery(".wsUrl").val();
      Socket = createSocketConnection(wsUrl, getCookie('IM_TOKEN'));
      socketEvent(Socket);
      layui.layer.close(index);
    },
    btn2: function (index) {
      layui.layer.close(index);
    }
  });
}

function wsSend(data) {
  Socket.send(data)
}

function socketEvent(webSocket) {
  webSocket.onopen = function (event) {
    wsOpen(event);
  };
  webSocket.onmessage = function (event) {
    wsReceive(event);
  };
  webSocket.onerror = function (event) {
    wsError(event)
  };
  webSocket.onclose = function (event) {
    wsClose(event)
  };
}

export {
  createSocketConnection,
  socketEvent,
  wsOpen,
  wsReceive,
  wsError,
  wsClose,
  wsSend,
  createMessage
}
