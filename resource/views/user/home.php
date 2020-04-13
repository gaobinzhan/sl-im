<!DOCTYPE html>
<html>
<?= $this->include('chat/header', ['title' => '聊天室']) ?>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">IM</div>
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

    </div>
  </div>

  <div class="layui-footer">
    <!-- 底部固定区域 -->
    © layui.com - 底部固定区域
    <span id="onlineNumber"></span>
  </div>
</div>
<input type="hidden" class="wsUrl" value="<?php echo $wsUrl ?>">
<script type="module" src="/chat/js/init.js"></script>
<script type="module">
  import {static_user_info} from '/chat/js/api.js';

  layui.use(['layer', 'jquery', 'element'], function () {
    var layer = layui.layer;
    var $ = layui.jquery;
    var element = layui.element;

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
        zIndex: layer.zIndex,
        shade: 0,
        content: url,
        success: function (layero) {
          layer.setTop(layero);
        }
      });

    });
  });
</script>
</body>
</html>
