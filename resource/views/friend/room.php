<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta name="description" content="php webrtc">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
  <meta itemprop="description" content="Video chat using the reference WebRTC application">
  <meta itemprop="name" content="WebRTC">
  <meta name="mobile-web-app-capable" content="yes">
  <title>视频</title>
  <link rel="stylesheet" href="/chat/css/bootstrap.min.css">
  <link rel="stylesheet" href="/layim/css/layui.css" type="text/css">
  <script src="/layim/layui.js" type="text/javascript"></script>
  <style>
    .videos {
      font-size: 0;
      height: 50%;
      float: left;
      width: 50%;
      padding: 10px;
    }

    .btn {
      margin: 20px;
      font-weight: normal;
      text-align: center;
      vertical-align: middle;
      cursor: pointer;
      background-image: none;
      white-space: nowrap;
      padding: 6px 12px;
      font-size: 13px;
      line-height: 1.428571429;
      border-radius: 2px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      color: #fff;
      background-color: #3276b1;
      border-color: #2c699d;
    }
  </style>
</head>

<body>
<div>
  <div class="videos">
    <h1>Local</h1>
    <video id="localVideo" autoplay></video>
  </div>
  <div class="videos">
    <h1>Remote</h1>
    <video id="remoteVideo" autoplay></video>
  </div>
</div>
<script src="/chat/js/adapter.js"></script>
<script type="module">
  import {createVideoSocket, socketEvent,setLoadingIndex} from '/chat/js/video.js';
  import {getCookie} from '/chat/js/util.js';

  layui.use(['layer'], function () {
    var layer = layui.layer;
    var loadingIndex = layer.load(2);
    setLoadingIndex(loadingIndex);
    let webRtcUrl = parent.layui.jquery(".webRtcUrl").val();
    let videoSocket = createVideoSocket(webRtcUrl, getCookie('IM_TOKEN'));
    socketEvent(videoSocket);
  });




</script>
</body>
</html>
