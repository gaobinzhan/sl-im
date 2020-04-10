layui.use(['layim'], function () {
  var layim = layui.layim;
  layim.on('tool(code)', function (insert, send, obj) { //事件中的tool为固定字符，而code则为过滤器，对应的是工具别名（alias）
    layer.prompt({
      title: '插入代码'
      , formType: 2
      , shade: 0
    }, function (text, index) {
      layer.close(index);
      insert('[pre class=layui-code]' + text + '[/pre]'); //将内容插入到编辑器，主要由insert完成
      //send(); //自动发送
    });
    console.log(this); //获取当前工具的DOM对象
    console.log(obj); //获得当前会话窗口的DOM对象、基础信息
  });
});
