
function Ajax() {
  var self = this;

  function getAjax() {
    var xmlhttp;
    if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest();
    else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    return xmlhttp;
  }

  function buildUri(params) {
    var uri = [];
    for(var k in params) {
      uri.push(k+'='+encodeURIComponent(params[k]));
    }
    uri = uri.join('&').replace(/%20/g, '+');
    return uri;
  }

  function post(resource, params, callback) {
    var xmlhttp = getAjax();
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

function urlValid(url) {
  return url.match(/^https?:\/\//);
}

function request() {
  var el = document.querySelector('input[name=url]');
  if (!urlValid(el.value)) {
    flash('<span style="color: red;">Wrong url format</span>', 2000);
    return;
  }
  var longUrl = el.value;
  ajax.post('/post', {url: el.value}, function(err, res){
    if(err) {
      flash('<span style="color: red;">'+err.message+'</span>', 2000);
      return console.error(err);
    } else if (!urlValid(res)) {
      flash('<span style="color: red;">'+res+'</span>', 2000);
      return;
    }
    var url = res;
    result('<a href="'+url+'">'+url+'</a>');
  })
}

var ajax = new Ajax;
