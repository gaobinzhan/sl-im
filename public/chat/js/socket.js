import {output, isEmpty} from "./util.js";
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
  if (cmd !== user_ping) {
    ack(msg);
  }
  return JSON.stringify(msg)
}

function ack(msg) {
  let data = msg.data;
  let message_id = data.message_id;
  messageList[message_id] = {
    msg: msg,
    timer: setTimeout(function () {
      if (!isEmpty(data.content)) {
        layui.layer.open({
          title: '消息发送失败',
          content: data.content
          , btn: ['重试', '取消']
          , yes: function (index, layero) {
            ack(messageList[message_id].msg);
            layui.layer.close(index);
          }
          , btn2: function (index, layero) {
            delete messageList[message_id];
            layui.layer.close(index);
          }
          , cancel: function () {
            delete messageList[message_id];
            layui.layer.close(index);
          }
        });
      }
    }, 10000)
  };
  output(messageList);
}

function wsOpen(event) {
  output(event, 'onOpen');
  heartbeat = setInterval(function () {
    Socket.send(createMessage(user_ping));
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

  let message_id = result.data.message_id;
  clearInterval(messageList[message_id].timer);
  delete messageList[message_id];
}

function wsError(event) {
  output(event, 'onError');
  clearInterval(heartbeat)
}

function wsClose(event) {
  output(event, 'onClose');
  clearInterval(heartbeat)
}

function wsSend(data) {
  Socket.send(data)
}

export {
  createSocketConnection,
  wsOpen,
  wsReceive,
  wsError,
  wsClose,
  wsSend,
  createMessage
}
