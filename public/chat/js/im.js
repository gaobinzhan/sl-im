import {output, isEmpty, getCookie} from "./util.js";
import {MessageActive} from './event.js';
import {
  user_ping_cmd,
  system_error_cmd,
  system_event_cmd,
  friend_get_unread_message_cmd,
  user_get_unread_application_count_cmd
} from "./api.js";


var Im;
var heartbeat;
var messageList = {};

function createSocketConnection(url, protocols) {
  output(url, 'createSocketConnection');
  Im = new WebSocket(url, protocols);
  return Im;
}

function createMessage(cmd, data = {}, ext = {}) {
  let msg = {
    cmd: cmd,
    data: data,
    ext: ext
  };
  output(msg);
  ack(msg);
  return JSON.stringify(msg);
}

function ack(msg, num = 1) {
  let data = msg.data;
  let message_id = data.message_id;
  if (isEmpty(message_id)) return false;
  messageList[message_id] = {
    msg: msg,
    num: num,
    timer: setTimeout(function () {
      output(num, message_id + '的发送次数');
      if (num > 3) {
        if (!isEmpty(data.content)) {
          output(num, '重试次数大于3进行提示');
          layui.layer.msg('消息发送失败：' + data.content, {
            time: 0
            , btn: ['重试', '取消']
            , yes: function (index) {
              Im.send(JSON.stringify(msg));
              ack(messageList[message_id].msg, messageList[message_id]['num'] + 1);
              layui.layer.close(index);
            },
            btn2: function (index) {
              delete messageList[message_id];
              layui.layer.close(index);
            }
          });
        }
      } else {
        Im.send(JSON.stringify(msg));
        ack(messageList[message_id].msg, messageList[message_id]['num'] + 1);
      }
    }, 5000)
  };
  output(messageList);
}


function wsOpen(event) {
  output(event, 'onOpen');
  heartbeat = setInterval(function () {
    wsSend(createMessage(user_ping_cmd));
  }, 10000);
  infoInit();
}

function infoInit() {
  wsSend(createMessage(friend_get_unread_message_cmd, {}));
  wsSend(createMessage(user_get_unread_application_count_cmd, {}))
}

function wsReceive(event) {
  let result = eval('(' + event.data + ')');
  output(result, 'onMessage');
  if (layui.jquery.isEmptyObject(result)) {
    return false;
  }
  if (result.cmd && result.cmd === system_error_cmd) {
    layer.msg(result.cmd + ' : ' + result.msg);
    clearMessageListTimer(result);
    return false;
  }

  if (result.cmd && result.cmd === system_event_cmd) {
    let method = result.method;
    MessageActive[method] ? MessageActive[method](result.data) : '';
    return false;
  }


  if (result.cmd && result.cmd === user_ping_cmd) {
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
  reloadSocket(event);
}

function wsClose(event) {
  output(event, 'onClose');
  clearInterval(heartbeat);
  reloadSocket(event)
}

function reloadSocket(event) {
  layui.layer.msg(event.reason, {
    time: 0
    , title: '连接异常关闭'
    , btn: ['重试', '取消']
    , yes: function (index) {
      var wsUrl = layui.jquery(".wsUrl").val();
      Im = createSocketConnection(wsUrl, getCookie('IM_TOKEN'));
      socketEvent(Im);
      layui.layer.close(index);
    },
    btn2: function (index) {
      layui.layer.close(index);
    }
  });
}

function wsSend(data) {
  Im.send(data)
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
