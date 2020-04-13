<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <style type="text/css">
    body {
      text-align: center
    }

    .layui-find-list li img {
      position: absolute;
      left: 15px;
      top: 8px;
      width: 36px;
      height: 36px;
      border-radius: 100%;
    }

    .layui-find-list li {
      position: relative;
      height: 90px;;
      padding: 5px 15px 5px 60px;
      font-size: 0;
      cursor: pointer;
    }

    .layui-find-list li * {
      display: inline-block;
      vertical-align: top;
      font-size: 14px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .layui-find-list li span {
      margin-top: 4px;
      max-width: 155px;
    }

    .layui-find-list li p {
      display: block;
      line-height: 18px;
      font-size: 12px;
      color: #999;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .back {
      cursor: pointer;
    }

    .lay_page {
      background: #fff;
      margin: 0 auto;
    }
  </style>
</head>
<?= $this->include('chat/header', ['title' => '查找群']) ?>
<body>
<div class="layui-form">
  <div class="layui-container" style="padding:0">
    <div class="layui-row layui-col-space3">
      <div class="layui-col-xs7" style="margin-top: 15px;">
        <input type="text" name="keyword" lay-verify="required" autocomplete="off" placeholder="请输入群编号/昵称"
               class="layui-input">
      </div>
      <div class="layui-col-xs1" style="margin-top: 15px;">
        <button class="layui-btn layui-icon" lay-submit lay-filter="search">&#xe615;</button>
      </div>
    </div>
    <div id="LAY_view"></div>
    <textarea title="消息模版" id="LAY_tpl" style="display:none;">
			<fieldset class="layui-elem-field layui-field-title">
			  <legend>{{ d.legend}}</legend>
			</fieldset>
			<div class="layui-row ">
					{{#  layui.each(d.data, function(index, item){ }}
					<div class="layui-col-xs3 layui-find-list">
						<li layim-event="add" data-index="0"
                data-gid="{{ item.groupId }}" data-name="{{item.groupName}}">
							<img src="{{item.avatar}}">
							<span>{{item.groupName}}({{item.groupId}})</span>
							<p>{{item.introduction}}  {{#  if(item.introduction == ''){ }}无{{#  } }} </p>
							<button class="layui-btn layui-btn-mini btncolor add" data-type="group"><i class="layui-icon">&#xe654;</i>加群</button>
						</li>
					</div>
					{{#  }); }}
			</div>
        </textarea>
    <div class="lay_page" id="LAY_page"></div>

  </div>

</div>
<script type="module">
  import {group_get_recommended, group_search, group_apply} from '/chat/js/api.js';
  import {getRequest, postRequest} from '/chat/js/request.js';
  import {addGroup} from '/chat/js/panel.js';

  layui.use(['layim', 'laypage', 'form', 'flow'], function () {
    var layim = layui.layim
      , layer = layui.layer
      , laytpl = layui.laytpl
      , form = layui.form
      , $ = layui.jquery
      , laypage = layui.laypage;

    $(function () {
      getRecommend();
    });

    function getRecommend() {
      getRequest(group_get_recommended, {}, function (data) {
        var html = laytpl(LAY_tpl.value).render({
          data: data,
          legend: '推荐群',
        });
        $('#LAY_view').html(html);
      });
    }

    form.on('submit(search)', function (data) {
      $("#LAY_page").css("display", "block");
      var keyword = data.field.keyword;

      postRequest(group_search, {keyword: keyword, page: 1, size: 20}, function (data) {
        laypage.render({
          elem: 'LAY_page'
          , count: data.count
          , limit: data.perPage
          , prev: '<i class="layui-icon">&#58970;</i>'
          , next: '<i class="layui-icon">&#xe65b;</i>'
          , layout: ['prev', 'next', 'count']
          , curr: 1
          , jump: function (obj, first) {
            if (first) {
              rendering(data.list);
              return false;
            }
            let page = obj.curr;
            postRequest(group_search, {keyword: keyword, page: page, size: 20}, function (data) {
              rendering(data.list)
            });
          }
        });

      })
    });

    function rendering(data) {
      var html = laytpl(LAY_tpl.value).render({
        data: data,
        legend: '<a class="back"><i class="layui-icon">&#xe65c;</i>返回</a> 查找结果',
      });
      $('#LAY_view').html(html);
    };
    $('body').on('click', '.add', function () {
      var li = $(this).parents('li');
      var groupname = li.attr('data-name');
      var group_id = li.attr('data-gid');
      var avatar = li.find("img").attr('src');
      layui.layim.add({
        type: 'group'
        , username: groupname
        , avatar: avatar
        , submit: function (group, remark, index) {
          postRequest(group_apply, {
              group_id: group_id,
              application_reason: remark
            }, function (data) {
              let param_type = typeof (data);
              if (param_type == "object") {
                addGroup(data)
              }
              layer.close(index);
            }, function (data) {
              layer.close(index);
            }
          );
        }
      });
    });
    $('body').on('click', '.back', function () {
      getRecommend();
      $("#LAY_page").css("display", "none");
    });
  })
  ;
</script>
</body>
</html>
