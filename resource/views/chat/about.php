<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<?= $this->include('chat/header', ['title' => '关于']) ?>
<body>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
  <legend>更新记录</legend>
</fieldset>
<ul class="layui-timeline">
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">2020-04-18 v1.1.0</div>
      <p>点对点视频聊天（WebRtc+Websocket）</p>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">2020-04-13 v1.0.0</div>
      <p> 登录注册（Http）</p>
      <p> 单点登录（Websocket）</p>
      <p> 私聊（Websocket）</p>
      <p> 群聊（Websocket）</p>
      <p> 在线人数（Websocket）</p>
      <p> 获取未读消息（Websocket）</p>
      <p> 好友在线状态（Websocket）</p>
      <p> 好友 查找 添加 同意 拒绝（Http+Websocket）</p>
      <p> 群 创建 查找 添加 同意 拒绝（Http+Websocket）</p>
      <p> 聊天记录存储</p>
      <p> 心跳检测</p>
      <p> 消息重发</p>
      <p> 断线重连</p>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">2020-03-30 哈哈哈 不知道怎么想的 要写这个</div>
    </div>
  </li>
</ul>

<script>
</script>

</body>
</html>
