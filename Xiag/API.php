<?php

namespace Xiag;

use Xiag\Utils\UrlShotener;
use Xiag\DB\Urls;

class API
{
  private $server;
  private $request;
  public function __construct($server, $request)
  {
    $this->server = $server;
    $this->request = $request;
  }

  public function action()
  {
    $method = $this->server['REQUEST_METHOD'];
    switch ($method) {
      case 'GET':
        return $this->redirectAction($this->server['REQUEST_URI']);
      break;
      case 'POST':
        return $this->shortAction($this->request);
      break;
      default:
        echo '404 Not Found';
    }
  }

  private function shortAction($form)
  {
    $svc = new UrlShotener($form['url']);
    echo $svc->getUrl();
  }

  private function redirectAction($uri)
  {
    $url = explode('/', $uri);
    $hash = end($url);
    $urlModel = Urls::getByHash($hash);
    if($urlModel) {
      $url = $urlModel['url'];
      header('Location: ' . $url, true, 301);
    } else {
      echo "Unknown URL";
    }
  }
}
