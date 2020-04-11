import {getRequest, postRequest} from "./request.js";
import {user_get_unread_application_count, ws_chat_url, user_set_status} from "./api.js";
import {createSocketConnection, wsOpen, wsReceive, wsError, wsClose} from "./socket.js";
import {getCookie, output} from "./util.js";

function ready() {
  layui.layim.on('ready', function (options) {
    getRequest(user_get_unread_application_count, {}, function (count) {
      if (count == 0) {
        return false;
      }
      layui.layim.msgbox(count)
    });

    var webSocket = createSocketConnection(ws_chat_url, getCookie('IM_TOKEN'));
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
  });
};

function toolCode() {
  layui.layim.on('tool(code)', function (insert, send, obj) {
    layer.prompt({
      title: '插入代码'
      , formType: 2
      , shade: 0
    }, function (text, index) {
      layer.close(index);
      insert('[pre class=layui-code]' + text + '[/pre]');
    });
  });
};

function userStatus() {
  layui.layim.on('online', function (status) {
    output(status, 'userStatus');
    let data = (status === 'hide') ? 0 : 1;
    postRequest(user_set_status, {status: data})
  });
}

var MessageActive = {
  setUserStatus: function (data) {
    output(data, 'setUserStatus');
    layui.layim.setFriendStatus(data.user_id, data.status)
  }
};

export {
  ready,
  toolCode,
  userStatus,
  MessageActive,
}
