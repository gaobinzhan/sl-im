<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <style type="text/css">
    .layim-msgbox {
      margin: 15px;
    }

    .layim-msgbox li {
      position: relative;
      margin-bottom: 10px;
      padding: 0 110px 10px 60px;
      padding-bottom: 10px;
      line-height: 22px;
      border-bottom: 1px dotted #e2e2e2;
      width: 200px;
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

    .pt15 {
      padding-top: 15px;
    }

    .pt10 {
      padding-top: 10px;
    }

    .pt30 {
      padding-top: 30px;
    }

    .pd0 {
      padding: 0px;
    }

    .chat {
      float: right;
      margin-top: -45px;
      margin-right: -110px;
      z-index: 999999;
    }

    .label {
      float: left;
      display: block;
      padding: 9px 5px 9px 20px;
      width: 40px;
      font-weight: 400;
      text-align: right;
    }

    .label_key {
      float: left;
      display: block;
      padding: 9px 5px;
      font-weight: 400;
    }

    .block {
      margin-left: 55px;
      min-height: 36px;
    }

    .layui-input, .layui-textarea {
      display: block;
      width: 90%;
      padding-left: 10px;
    }

    .noresize {
      resize: none;
    }

    .select {
      height: 38px;
      line-height: 38px;
      border: 1px solid #e6e6e6;
      background-color: #fff;
      border-radius: 2px;
    }
  </style>
</head>
<?= $this->include('chat/header', ['title' => '个人资料']) ?>
<body>
<div class="layui-form" id="LAY_view">

</div>

<script type="text/html" title="资料模版" id="LAY_tpl" style="display:none;">
  <form class="layui-form" action="" style="margin-top: 15px;">
    <div class="layui-form-item layui-col-xs11">
      <label class="layui-form-label">用户昵称</label>
      <div class="layui-input-block">
        <input type="text" name="username" lay-verify="required" autocomplete="off"
               placeholder="输入用户昵称" value="{{d.username}}" class="layui-input">
      </div>
    </div>
    <div class="layui-form-item layui-col-xs11">
      <label class="layui-form-label">用户头像</label>
      <div class="layui-input-block">
        <input type="text" name="avatar" lay-verify="required|url" autocomplete="off"
               placeholder="输入url即可" value="{{d.avatar}}" class="layui-input">
      </div>
    </div>
    <div class="layui-form-item layui-col-xs11">
      <label class="layui-form-label">注册时间</label>
      <p class="layim-msgbox-user" style="line-height: 28px">
        {{d.createdAt}}
      </p>
    </div>
    <div class="layui-form-item" style="padding-top: 30px;">
      <div class="layui-input-block">
        <button class="layui-btn" lay-submit lay-filter="save">保存</button>
      </div>
    </div>
  </form>
</script>
</body>
<script type="module">
  import {user_info, user_change_name_avatar} from '/chat/js/api.js';
  import {getRequest, postRequest} from '/chat/js/request.js';

  layui.use(['form', 'laydate'], function () {
    var form = layui.form
      , laydate = layui.laydate;
    layui.use(['laydate', 'form', 'laytpl'], function () {
      var layim = layui.layim
        , layer = layui.layer
        , laytpl = layui.laytpl
        , $ = layui.jquery;

      form.on("submit(save)", function (data) {
        postRequest(user_change_name_avatar, data.field, function (result) {
          parent.layui.$(".layui-layim-user").html(data.field.username);
          parent.layui.$(".layui-nav-img").attr('src',data.field.avatar);
          setTimeout(function () {
            let index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
          }, 1000);
        });
        return false;
      });
      getRequest(user_info, {}, function (data) {
        var html = laytpl(LAY_tpl.innerHTML).render(data);
        $('#LAY_view').html(html);
      });
    });
  });
</script>
</html>
