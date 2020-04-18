import {postRequest} from "./request.js";
import {
  user_set_status,
  friend_send_cmd,
  group_send_cmd,
  friend_read_msg_cmd,
  user_set_sign,
  static_friend_room,
  friend_video_busy
} from "./api.js";
import {
  createSocketConnection,
  createMessage,
  socketEvent,
  wsSend
} from "./im.js";
import {getCookie, output, messageId} from "./util.js";
import {addFriend, addGroup} from "./panel.js";

function ready() {
  layui.layim.on('ready', function (options) {
    var wsUrl = layui.jquery(".wsUrl").val();
    var webSocket = createSocketConnection(wsUrl, getCookie('IM_TOKEN'));
    socketEvent(webSocket);
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

function videoRoom() {
  layui.layim.on('tool(video)', function (insert, send, obj) {
    output(obj, '视频聊天触发');
    if (obj.data.type === 'group') {
      layui.layer.msg('当前不支持群视频！');
      return false;
    }
    wsSend(createMessage(friend_video_busy, {to_user_id: obj.data.id}));
  });
}

function userStatus() {
  layui.layim.on('online', function (status) {
    output(status, 'userStatus');
    let data = (status === 'hide') ? 0 : 1;
    postRequest(user_set_status, {status: data})
  });
}


function userSign() {
  layui.layim.on('sign', function (value) {
    postRequest(user_set_sign, {sign: value});
  });
}

function toMessage() {
  layui.layim.on('sendMessage', function (res) {
    output(res, 'toMessage');
    let cmd = (res.to.type === 'friend') ? friend_send_cmd : group_send_cmd;
    let data = {
      message_id: messageId(),
      from_user_id: res.mine.id,
      to_id: parseInt(res.to.id),
      content: res.mine.content
    };
    let msg = createMessage(cmd, data);
    wsSend(msg);
  });
};

function alertVideoRoom(url, title, roomId) {
  layui.layer.open({
    type: 2,
    title: title,
    area: ['1000px', '600px'],
    maxmin: true,
    shade: 0,
    content: url + '?room_id=' + roomId,
  });
}

var MessageActive = {
  setUserStatus: function (data) {
    output(data, 'setUserStatus');
    layui.layim.setFriendStatus(data.user_id, data.status)
  },
  getMessage: function (data) {
    output(data, 'getMessage');
    layui.layim.getMessage(data);
    if (data.type === 'friend') {
      let msg = createMessage(friend_read_msg_cmd, {
        'message_id': data.cid
      });
      wsSend(msg)
    }
  },
  onlineNumber: function (data) {
    layui.jquery("#onlineNumber").html(data)
  },
  getUnreadApplicationCount: function (data) {
    if (data === 0) return false;
    layui.layim.msgbox(data)
  },
  friendAgreeApply: function (data) {
    addFriend(data);
  },
  groupAgreeApply: function (data) {
    addGroup(data);
  },
  friendVideoRoom: function (data) {
    let mineId = layui.layim.cache().mine.id;
    let title = '与 ' + ((data.userId === mineId) ? data.toUserName : data.fromUserName) + ' 视频聊天';
    let roomId = data.roomId;
    if (data.userId === mineId) {
      alertVideoRoom(static_friend_room, title, data.roomId);
    }
    if (data.userId !== mineId) {
      layui.layer.msg(data.fromUserName + ' 向您发起了视频聊天', {
        time: 10000
        , btn: ['接受', '拒绝']
        , yes: function (index) {
          layui.layer.close(index);
          alertVideoRoom(static_friend_room, title, roomId);
        }
      });
    }
  }
};

export {
  ready,
  toolCode,
  userStatus,
  userSign,
  MessageActive,
  toMessage,
  videoRoom
}
