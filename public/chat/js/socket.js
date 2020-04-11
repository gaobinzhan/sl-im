import {output} from "./util.js";
import {MessageActive} from './event.js';

var Socket;

function createSocketConnection(url, protocols) {
  output(url, 'createSocketConnection');
  Socket = new WebSocket(url, protocols);
  return Socket;
}

function wsOpen(event) {
  output(event, 'onOpen');
}

function wsReceive(event) {
  let result = eval('(' + event.data + ')');
  output(result, 'onMessage');
  if (layui.jquery.isEmptyObject(result)) {
    return false;
  }
  if (result.code && result.code !== 0) {
    layer.msg(result.code + ' : ' + result.msg);
    return false;
  }

  if (result.type === 'event') {
    let method = result.method;
    MessageActive[method] ? MessageActive[method](result.data) : '';
  }
}

function wsError(event) {
  output(event, 'onError')

}

function wsClose(event) {
  output(event, 'onClose')

}

export {
  createSocketConnection,
  wsOpen,
  wsReceive,
  wsError,
  wsClose
}
