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
<?= $this->include('chat/header', ['title' => '好友资料']) ?>
<body>
<div class="layui-form" id="LAY_view">

</div>
<script type="text/html" title="资料模版" id="LAY_tpl" style="display:none;">
  <div class="layui-form-item" style="padding-top: 15px;">
    <div class="layim-msgbox">
      <li>
        <a href="javascript:void(0);" target="_blank">
          <img src="{{ d.avatar }}"
               class="layui-circle layim-msgbox-avatar">
        </a>
        <p class="layim-msgbox-user">
          <span style="letter-spacing: 5px;">编 号</span> {{ d.groupId }}
        </p>
        <p class="layim-msgbox-user">
          <span style="letter-spacing: 5px;">群 昵 称</span> {{ d.groupName }}
        </p>
      </li>
    </div>

  </div>
  <div class="layui-col-xs12 pt10">
    <label class="label">创建时间</label>
    <div class="block">
      <div class="label_key">{{d.createdAt}}</div>
    </div>
  </div>
  <div class="layui-col-xs12 pt10">
    <label class="label">规 模</label>
    <div class="block">
      <div class="label_key">{{d.size}}人</div>
    </div>
  </div>
  <div class="layui-col-xs12 pt10">
    <label class="label">介 绍</label>
    <div class="block">
      <div class="label_key">{{d.introduction}}</div>
    </div>
  </div>
</script>
</body>
<script type="module">
  import {getQueryValue} from '/chat/js/util.js';
  import {group_info} from '/chat/js/api.js';
  import {getRequest} from '/chat/js/request.js';

  layui.use(['laydate', 'form', 'laytpl', 'laydate'], function () {
    var form = layui.form
      , laydate = layui.laydate;
    var layim = layui.layim
      , layer = layui.layer
      , laytpl = layui.laytpl
      , $ = layui.jquery;

    let id = getQueryValue('id');
    getRequest(group_info, {group_id: id}, function (data) {
      var html = laytpl(LAY_tpl.innerHTML).render(data);
      $('#LAY_view').html(html);
    }, function () {
      setTimeout(function () {
        let index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
      }, 1000)
    });
  });


</script>
</html>
