<!DOCTYPE html>
<html>
<?= $this->include('chat/header', ['title' => '聊天室']) ?>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">SL-IM</div>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;"><img src="<?= $userInfo['avatar'] ?>" class="layui-nav-img">
          <span class="layui-layim-user"><?= $userInfo['username'] ?></span>
        </a>
        <dl class="layui-nav-child">
          <dd><a href="javascript:;" class="userInfo">个人资料</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="/user/signOut">退出</a></li>
    </ul>
  </div>


  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree" lay-filter="test">
        <?php foreach ($menus as $menu) {
          echo <<<EOF
        <li class="layui-nav-item">
          <a href="javascript:;">{$menu['title']}</a>
EOF;
          foreach ($menu['child'] as $child) {
            echo <<<EOF
          <dl class="layui-nav-child">
            <dd><a href="javascript:;" class="addIframe" im-width="{$child['width']}" im-height="{$child['height']}" im-title="{$child['title']}" im-id="{$child['id']}" im-url="{$child['url']}">{$child['title']}</a></dd>
          </dl>
EOF;
          }
          echo "
        </li>";
        }
        ?>
      </ul>
    </div>
  </div>


  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
      <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend>sl-im信息</legend>
      </fieldset>
      <div class="layui-collapse" lay-filter="test">
        <div class="layui-colla-item">
          <h2 class="layui-colla-title">Github</h2>
          <div class="layui-colla-content">
            <a href="https://github.com/gaobinzhan/sl-im" target="_blank">https://github.com/gaobinzhan/sl-im</a>
          </div>
        </div>
      </div>
      <div class="layui-collapse" lay-filter="test">
        <div class="layui-colla-item">
          <h2 class="layui-colla-title">介绍</h2>
          <div class="layui-colla-content">
            <p><a href="https://im.gaobinzhan.com" target="_blank">sl-im</a> 是基于 <a href="https://www.swoft.org"
                                                                                    target="_blank">Swoft</a> 微服务协程框架和
              <a href="https://www.layui.com/layim/" target="_blank">Layim</a> 网页聊天系统 所开发出来的聊天室。</p>
          </div>
        </div>
      </div>
      <div class="layui-collapse" lay-filter="test">
        <div class="layui-colla-item">
          <h2 class="layui-colla-title">联系方式</h2>
          <div class="layui-colla-content">
            <p>WeChat：<b>gaobinzhan</b></p>
            <p>Email：<b>gaobinzhan@gmail.com</b></p>
            <p>QQ：<b>975975398</b></p>
          </div>
        </div>
      </div>
    </div>

    <div style="padding: 20px; background-color: #F2F2F2;">
      <div class="layui-row layui-col-space15">
        <div class="layui-col-md6">
          <div class="layui-card">
            <div class="layui-card-header">公众号</div>
            <div class="layui-card-body">
              <p align="center">
                <img src="https://qiniu.gaobinzhan.com/2020/04/13/1aba16c932df2.jpg" alt="">
              </p>
            </div>
          </div>
        </div>
        <div class="layui-col-md6">
          <div class="layui-card">
            <div class="layui-card-header">小程序</div>
            <div class="layui-card-body">
              <p align="center">
                <img src="https://qiniu.gaobinzhan.com/2020/04/13/0a23783b1ec47.jpg" alt="">
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="layui-footer">
    <!-- 底部固定区域 -->
    <span style="font-size:16px;">© </span>2020 ICP证：<a class="record" href="http://www.miitbeian.gov.cn/"
                                                        target="_block">鲁ICP备18054315号-1</a>
    <span id="onlineNumber"></span>
    <script type="text/javascript">document.write(unescape("%3Cspan id='cnzz_stat_icon_1278808980'%3E%3C/span%3E%3Cscript src='https://s9.cnzz.com/z_stat.php%3Fid%3D1278808980%26online%3D1%26show%3Dline' type='text/javascript'%3E%3C/script%3E"));</script>
  </div>
</div>
<input type="hidden" class="wsUrl" value="<?php echo $wsUrl ?>">
<input type="hidden" class="webRtcUrl" value="<?php echo $webRtcUrl ?>">
<input type="hidden" class="stunServer" value="<?php echo $stunServer ?>">
<script type="module" src="/chat/js/init.js"></script>
<script type="module">
  import {static_user_info} from '/chat/js/api.js';

  layui.use(['layer', 'jquery', 'element', 'code'], function () {
    var layer = layui.layer;
    var $ = layui.jquery;
    var element = layui.element;
    layui.code(); //引用code方法
    $(".userInfo").click(function () {
      layer.open({
        title: '用户资料',
        type: 2,
        closeBtn: 1,
        area: ['400px', '300px'],
        id: 'userInfo',
        maxmin: true,
        zIndex: layer.zIndex,
        shade: 0,
        content: static_user_info,
        success: function (layero) {
          layer.setTop(layero);
        }
      });
    });

    $(".addIframe").click(function (e) {
      let title = $(this).attr('im-title');
      let id = $(this).attr('im-id');
      let url = $(this).attr('im-url');
      let width = $(this).attr('im-width');
      let height = $(this).attr('im-height');
      layer.open({
        title: title,
        type: 2,
        closeBtn: 1,
        area: [width, height],
        id: id,
        maxmin: true,
        shade: 0,
        content: url,
        success: function (layero) {
        }
      });

    });
  });
</script>
</body>
</html>
