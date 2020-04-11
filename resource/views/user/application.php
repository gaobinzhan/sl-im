<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <style>
    .layim-msgbox {
      margin: 15px;
    }

    .layim-msgbox li {
      position: relative;
      margin-bottom: 10px;
      padding: 0 130px 10px 60px;
      padding-bottom: 10px;
      line-height: 22px;
      border-bottom: 1px dotted #e2e2e2;
    }

    .layim-msgbox .layim-msgbox-tips {
      margin: 0;
      padding: 10px 0;
      border: none;
      text-align: center;
      color: #999;
    }

    .layim-msgbox .layim-msgbox-system {
      padding: 0 10px 10px 10px;
    }

    .layim-msgbox li p span {
      padding-left: 5px;
      color: #999;
    }

    .layim-msgbox li p em {
      font-style: normal;
      color: #FF5722;
    }

    .layim-msgbox-avatar {
      position: absolute;
      left: 0;
      top: 0;
      width: 50px;
      height: 50px;
    }

    .layim-msgbox-user {
      padding-top: 5px;
    }

    .layim-msgbox-content {
      margin-top: 3px;
    }

    .layim-msgbox .layui-btn-small {
      padding: 0 15px;
      margin-left: 5px;
    }

    .layim-msgbox-btn {
      position: absolute;
      right: 0;
      top: 12px;
      color: #999;
    }
  </style>
</head>
<?= $this->include('chat/header', ['title' => '消息盒子']) ?>
<body>

<ul class="layim-msgbox" id="LAY_view"></ul>
<textarea title="消息模版" id="LAY_tpl" style="display:none;">
            {{# layui.each(d.data, function(index, item){
                if(item.application_role == 'create'){ }}
                        <li data-uid="{{ item.receiver_id }}">
                          <a href="javascript:void(0);">
                            <img src="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.receiver_avatar }}
                                {{# }else{ }}
                                  {{ item.group_avatar }}
                                {{# } }}" class="layui-circle layim-msgbox-avatar">
                          </a>
                          <p class="layim-msgbox-user">
                            <a href="javascript:void(0);">
                              <b data-chat="{{item.application_type}}"
                                 data-id="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.receiver_id }}
                                {{# }else{ }}
                                  {{ item.group_id }}
                                {{# } }}" class="info">
                                {{# if(item.application_type == 'friend'){ }}
                                  {{ item.receiver_name }}
                                {{# }else{ }}
                                  {{ item.group_name }}
                                {{# } }}
                              </b>
                            </a>
                            <span>{{ item.created_at }}</span>
                          </p>
                          <p class="layim-msgbox-content">
                            {{# if(item.application_type == 'friend'){ }}
                            申请添加对方为好友
                            {{# }else{ }}
                            申请加入该群
                            {{# } }}
                            <span>{{ item.application_reason ? '附言: '+item.application_reason : '' }}</span>
                          </p>
                          <p class="layim-msgbox-btn">
                            等待验证
                          </p>
                        </li>


                {{# } else if(item.application_role == 'receiver') { }}



                  {{#  if(item.application_status == 0){ }}
                    <li data-uid="{{ item.from }}" data-id="{{ item.msgIdx }}" data-type="{{item.msgType}}"
                        data-name="{{ item.name }}"">
                                                <a href="javascript:void(0);">
                                                  <img src="{{ item.user_avatar }}"
                                                       class="layui-circle layim-msgbox-avatar">
                                                </a>
                                                <p class="layim-msgbox-user">
                                                  <a href="javascript:void(0);">
                                                    <b data-chat="friend"
                                                       data-id="{{ item.user_id }}" class="info">
                                                      {{ item.user_name }}</b></a>
                                                  <span>{{ item.created_at }}</span>
                                                </p>
                                                <p class="layim-msgbox-content">
                                                  {{# if(item.application_type == 'friend'){ }}
                                                  申请添加你为好友
                                                  {{# }else{ }}
                                                  申请加入 <b data-chat="group"
                                                          data-id="{{item.group_id}}"
                                                          class="info">{{ item.group_name }}</b> 群
                                                  {{# } }}
                                                  <span>{{ item.application_reason ? '附言: '+item.application_reason : '' }}</span>
                                                </p>
                                                <p class="layim-msgbox-btn">
                                                  <button class="layui-btn layui-btn-small"
                                                          data-type="agree"
                                                          data-name="{{item.user_name}}"
                                                          data-avatar="{{item.user_avatar}}"
                                                          data-chat="{{item.application_type}}"
                                                          data-user-application-id="{{item.user_application_id}}">同意</button>
                                                  <button class="layui-btn layui-btn-small layui-btn-primary"
                                                          data-type="refuse"
                                                          data-chat="{{item.application_type}}"
                                                          data-user-application-id="{{item.user_application_id}}">拒绝</button>
                                                </p>
  </li>

  {{#  } else { }}
                        <li>
                          <a href="javascript:void(0);">
                            <img src="{{ item.user_avatar }}" class="layui-circle layim-msgbox-avatar">
                          </a>
                          <p class="layim-msgbox-user">
                            <a href="javascript:void(0);"><b data-chat="friend"
                                                             data-id="{{ item.user_id }}" class="info">{{ item.user_name }}</b></a>
                            <span>{{ item.created_at }}</span>
                          </p>
                          <p class="layim-msgbox-content">
                            {{# if(item.application_type == 'friend'){ }}
                                                  申请添加你为好友
                                                  {{# }else{ }}
                            申请加入 <b data-chat="group"
                                    data-id="{{ item.group_id }}" class="info">{{ item.group_name }}</b> 群
                                                  {{# } }}
                            <span>{{ item.application_reason ? '附言: '+item.application_reason : '' }}</span>
                            {{# if(item.application_status == 1){ }}
                            <button class="layui-btn layui-btn-xs btncolor" data-name="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.user_name }}
                                {{# }else{ }}
                                  {{ item.group_name }}
                                {{# } }}"
                                    data-chat="{{item.application_type}}" data-type="chat" data-id="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.user_id }}
                                {{# }else{ }}
                                  {{ item.group_id }}
                                {{# } }}" data-avatar="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.user_avatar }}
                                {{# }else{ }}
                                  {{ item.group_avatar }}
                                {{# } }}">发起会话</button>
                            {{# } }}
                          </p>
                          <p class="layim-msgbox-btn">
                            {{ item.application_status_text }}
                          </p>
                        </li>

                  {{#  } }}




                {{# }else if(item.application_role == 'system'){ }}

                  {{#  if(item.application_type == 'friend'){ }}

                      <li class="layim-msgbox-system">
                          <p><em>系统：</em><b data-chat="friend"
                                            data-id="{{ item.receiver_id }}"
                                            class="info">{{ item.receiver_name }}</b>
                          {{# if(item.application_status == 1){ }}
                          已同意你的好友申请
                            <button class="layui-btn layui-btn-xs btncolor" data-name="{{ item.receiver_name }}"
                                    data-chat="{{item.application_type}}" data-type="chat"
                                    data-id="{{ item.receiver_id }}"
                                    data-avatar="{{ item.receiver_avatar }}">发起会话</button>
                          {{# }else{ }}
                          已拒绝你的好友申请
                          {{# } }}
                          <span>{{ item.updated_at }}</span></p>
                      </li>
                  {{#  } else { }}

                      <li class="layim-msgbox-system">
                            <p><em>系统：</em> 管理员 <b data-chat="friend"
                                                   data-id="{{ item.receiver_id }}" class="info">{{ item.receiver_name }}</b>
                            {{# if(item.application_status == 1){ }}
                            已同意你加入群 <b data-chat="group"
                                       data-id="{{ item.group_id }}" class="info">{{ item.group_name }}</b>
                              <button class="layui-btn layui-btn-xs btncolor"
                                      data-name="{{ item.group_name }}"
                                      data-chat="{{item.application_type}}" data-type="chat"
                                      data-id="{{ item.group_id }}" data-avatar="{{# if(item.application_type == 'friend'){ }}
                                  {{ item.user_avatar }}
                                {{# }else{ }}
                                  {{ item.group_avatar }}
                                {{# } }}">发起会话</button>
                            {{# }else{ }}
                            已拒绝你加入群 <b data-chat="group"
                                       data-id="{{ item.group_id }}" class="info">{{ item.group_name }}</b>
                            {{# } }}
                            <span>{{ item.updated_at }}</span></p>
                        </li>
                  {{#  } }}

                {{# }
            }); }}
        </textarea>

<script type="module">
  import {
    user_get_application,
    static_friend_info,
    static_group_info,
    friend_agree_apply,
    friend_refuse_apply,
    group_agree_apply,
    group_refuse_apply,
  } from '/chat/js/api.js';
  import {getRequest, postRequest} from '/chat/js/request.js';
  import {addFriend} from '/chat/js/panel.js';

  layui.use(['layim', 'flow'], function () {
    var layim = layui.layim, layer = layui.layer, laytpl = layui.laytpl, $ = layui.jquery, flow = layui.flow;

    var formatDate = function (now) {
      var myDate = new Date(now);
      var month = myDate.getMonth() + 1;
      var date = myDate.getDate();
      return month + "月" + date + "日";
    };
    var renderMsg = function (page, callback) {
      postRequest(user_get_application, {
        page: page || 1,
        size: 20
      }, function (data) {
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
    $('body').on('click', '.info', function () {
      let type = $(this).attr('data-chat').trim();
      let id = $(this).attr('data-id').trim();
      let url = (type == 'friend') ? static_friend_info : static_group_info;
      parent.layer.open({
        title: id + '的资料',
        type: 2,
        closeBtn: 1,
        area: ['400px', '300px'],
        id: id + type,
        maxmin: true,
        zIndex: layer.zIndex,
        shade: 0,
        content: url + '?id=' + id,
        success: function (layero, index) {
          layer.setTop(layero);
        }
      });
    });
    var active = {
      chat: function (data) {
        parent.layui.layim.chat({
          name: data.attr('data-name')
          , type: data.attr('data-chat')
          , avatar: data.attr('data-avatar')
          , id: data.attr('data-id').trim()
        });
      },
      agree: function () {
        let that = $(this);
        let type = $(this).attr('data-chat');
        let name = $(this).attr('data-name');
        let avatar = $(this).attr('data-avatar');
        let id = $(this).attr('data-user-application-id');
        if (type == 'friend') {
          parent.layui.layim.setFriendGroup({
            type: 'friend'
            , username: name
            , avatar: avatar
            , group: parent.layui.layim.cache().friend
            , submit: function (group, index) {
              getRequest(friend_agree_apply, {user_application_id: id, friend_group_id: group}, function (res) {
                addFriend(res);
                that.parents(".layim-msgbox-btn").html("已同意");
                parent.layer.close(index);
              }, function (res) {
                parent.layer.close(index);
              })
            }
          });
        } else {
          getRequest(group_agree_apply, {user_application_id: id}, function (res) {
            that.parents(".layim-msgbox-btn").html("已同意");
          });
        }
      }
      , refuse: function () {
        let that = $(this);
        let type = $(this).attr('data-chat');
        let url = (type == 'friend') ? friend_refuse_apply : group_refuse_apply;
        let id = $(this).attr('data-user-application-id');
        layer.confirm('确定拒绝吗？', function (index) {
          getRequest(url, {user_application_id: id}, function (res) {
            that.parents(".layim-msgbox-btn").html("已拒绝");
          })
        });
      }

    };
    $('body').on('click', '.layui-btn', function () {
      var othis = $(this), type = othis.data('type');
      active[type] ? active[type].call(this, othis) : '';
    });

  });
</script>
</body>
</html>
