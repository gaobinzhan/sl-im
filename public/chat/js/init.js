import {
  user_init,
  static_user_application,
  static_user_chat_history,
  group_get_relation,
  util_upload_img,
  util_upload_file
} from "./api.js";
import {getCookie} from "./util.js";
import {toolCode, ready,videoRoom, userStatus, userSign, toMessage} from "./event.js";

layui.use('layim', function (layim) {
  layim.config({
    init: {
      url: user_init,
      type: 'get',
      data: {
        token: getCookie('IM_TOKEN')
      }
    }

    , members: {
      url: group_get_relation
      , type: 'get'
      , data: {
        token: getCookie('IM_TOKEN')
      }
    }
    , uploadImage: {
      url: util_upload_img + '?token=' + getCookie('IM_TOKEN')
    }
    , uploadFile: {
      url: util_upload_file + '?token=' + getCookie('IM_TOKEN')
    }
    , tool: [
      {
        alias: 'code'
        , title: '代码'
        , icon: '&#xe64e;'
      }
      , {
        alias: 'video'
        , title: '视频通话'
        , icon: '&#xe643;'
      }
    ]
    , brief: false
    , title: '我的IM'
    , min: false
    , right: "0px"
    , minRight: ''
    , initSkin: ''
    , isAudio: true
    , isVideo: true
    , notice: true
    , voice: 'default.mp3'
    , isfriend: true
    , isgroup: true
    , maxLength: '3000'
    , copyright: true
    , msgbox: static_user_application
    , chatLog: static_user_chat_history
  })
  ;
  toolCode();
  videoRoom();
  ready();
  userStatus();
  userSign();
  toMessage();
});
