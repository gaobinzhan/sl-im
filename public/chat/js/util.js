let debug = true;

function output(info) {
  if (debug) {
    let title = arguments[1] || '';
    console.log("============ " + title + " start");
    console.log(info);
    console.log("============ " + title + " end");
  }
}

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i].trim();
    if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
  }
  return "";
}

function getQueryValue(queryName) {
  var query = decodeURI(window.location.search.substring(1));
  var vars = query.split("&");
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0] == queryName) {
      return pair[1];
    }
  }
  return null;
}

function messageId() {
  return Number(Math.random().toString().substr(3, 3) + Date.now()).toString(36)
}

function isEmpty(obj) {
  if (typeof obj == "undefined" || obj == null || obj === "") {
    return true;
  } else {
    return false;
  }
}

export {
  output,
  getCookie,
  getQueryValue,
  messageId,
  isEmpty
}






