
function Ajax() {
  var self = this;
  var xmlhttp;
  if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest();
  else xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

  function buildUri(params) {
    var uri = [];
    for(var k in params) {
      uri.push(k+'='+encodeURIComponent(params[k]));
    }
    uri = uri.join('&').replace(/%20/g, '+');
    return uri;
  }

  function post(resource, params, callback) {
    xmlhttp.onreadystatechange=function() {
      if (xmlhttp.readyState==4) {
        if (xmlhttp.status !== 200)
          return callback(new Error(xmlhttp.responseText))
        callback(null, xmlhttp.responseText);
      }
    }
    xmlhttp.open("POST", resource, true);
    xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    xmlhttp.send(buildUri(params));
  }

  self.post = post;
}

function flash(msg, time) {
  var el = document.querySelector('#result');
  if (el._t) clearTimeout(el._t);
  if(el._html !== null) el._html = el.innerHTML;
  el.innerHTML = msg;

  el._t = setTimeout(function(){
    el.innerHTML = el._html;
    el._html = el._t = null;
  }, time);
}

function result(msg) {
  var el = document.querySelector('#result');
  if (el._t) clearTimeout(el._t);
  el.innerHTML = msg;
  el._html = el._t = null;
}

function request() {
  var el = document.querySelector('input[name=url]');
  if (!el.validity.valid) {
    flash('<span style="color: red;">Wrong url format</span>', 2000);
    return;
  }
  var longUrl = el.value;
  ajax.post('/', {url: el.value}, function(err, res){
    if(err) {
      flash('<span style="color: red;">err.message</span>', 200);
      return console.err(err);
    }
    var url = res;
    result('<a href="'+url+'">'+url+'</a>');
  })
}

var ajax = new Ajax;
