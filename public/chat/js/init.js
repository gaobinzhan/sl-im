import {
  user_init,
  static_user_application,
  static_user_chat_history,
  user_get_unread_application_count,
  group_get_relation
} from "./api.js";
import {output, getCookie} from "./util.js";
import {getRequest} from "./request.js";

layui.use('layim', function (layim) {
  //基础配置
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

    , tool: [{
      alias: 'code' //工具别名
      , title: '代码' //工具名称
      , icon: '&#xe64e;' //工具图标，参考图标文档
    }]

    , msgbox: static_user_application
    , chatLog: static_user_chat_history
  });
  layim.on('ready', function (options) {
    getRequest(user_get_unread_application_count, {}, function (count) {
      if (count == 0) {
        return false;
      }
      layim.msgbox(count)
    })
  });
});
