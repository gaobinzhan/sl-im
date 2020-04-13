<!DOCTYPE html>
<html>
<head>
  <style>
    body .layim-chat-main {
      height: auto;
    }
  </style>
</head>
<?= $this->include('chat/header', ['title' => '聊天记录']) ?>
<body>

<div class="layim-chat-main">
  <ul id="LAY_view"></ul>
</div>

<textarea title="消息模版" id="LAY_tpl" style="display:none;">
{{# layui.each(d.data, function(index, item){
  if(item.id == parent.layui.layim.cache().mine.id){ }}
    <li class="layim-chat-mine"><div class="layim-chat-user"><img src="{{ item.avatar }}"><cite><i>{{ layui.data.date(item.timestamp) }}</i>{{ item.username }}</cite></div><div
        class="layim-chat-text">{{ layui.layim.content(item.content) }}</div></li>
  {{# } else { }}
    <li><div class="layim-chat-user"><img src="{{ item.avatar }}"><cite>{{ item.username }}<i>{{ layui.data.date(item.timestamp) }}</i></cite></div><div
        class="layim-chat-text">{{ layui.layim.content(item.content) }}</div></li>
  {{# }
}); }}
</textarea>

<!--
上述模版采用了 laytpl 语法，不了解的同学可以去看下文档：http://www.layui.com/doc/modules/laytpl.html

-->


<script type="module">
  import {friend_get_chat_history, group_get_chat_history} from '/chat/js/api.js';
  import {getQueryValue} from '/chat/js/util.js';
  import {postRequest} from '/chat/js/request.js';

  layui.use(['layim', 'laypage', 'flow'], function () {
    var layim = layui.layim
      , layer = layui.layer
      , laytpl = layui.laytpl
      , $ = layui.jquery
      , laypage = layui.laypage;
    var flow = layui.flow;

    let id = getQueryValue('id');
    let type = getQueryValue('type');

    let url = (type === 'friend') ? friend_get_chat_history : group_get_chat_history;
    let params = (type === 'friend') ? {
      from_user_id: id
    } : {
      to_group_id: id
    };

    var renderMsg = function (page, callback) {
      params['page'] = page || 1;
      params['size'] = 20;
      postRequest(url, params, function (data) {
        let list = data.list;
        callback && callback(list, data.pageCount);
      });
    };

    flow.load({
      elem: '#LAY_view'
      , isAuto: false
      , end: '<li class="layim-msgbox-tips">暂无更多新消息</li>'
      , done: function (page, next) {
        renderMsg(page, function (data, pages) {
          var html = laytpl(LAY_tpl.value).render({
            data: data
            , page: page
          });
          next(html, page < pages);
        });
      }
    });
  });
</script>
</body>
</html>
