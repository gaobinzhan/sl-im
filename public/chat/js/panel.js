function addFriendGroup(data) {
  parent.layui.layim.cache().friend.push(data);
  let li = parent.layui.jquery(".layim-list-friend").find('>li').first();
  if (li.find('>ul').find('>li').html() == '暂无联系人') {
    li.remove();
  }
  let html = '<li><h5 layim-event="spread" lay-type="spread"><i class="layui-icon"></i><span>' + data.groupname + '</span><em>(<cite class="layim-count"> 0</cite>)</em></h5><ul class="layui-layim-list"><li class="layim-null">该分组下暂无好友</li></ul></li>';
  parent.layui.jquery(".layim-list-friend").append(html)
};

function addGroup(data) {
  parent.layui.layim.addList({
    type: 'group'
    , avatar: data.avatar
    , groupname: data.groupName
    , id: data.groupId
  })
};

function addFriend(data) {
  parent.layui.layim.addList({
    type: 'friend'
    , avatar: data.avatar
    , username: data.username
    , groupid: data.groupid
    , id: data.id
    , sign: data.sign
    , status: data.status
  })
};

export {
  addFriendGroup,
  addGroup,
  addFriend
};
