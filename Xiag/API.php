<?php

namespace Xiag;

use Xiag\Utils\Hasher;
use Xiag\DB\Urls;

use Xiag\Core\Config;
use Xiag\Core\DB;
use Xiag\Core\Cache;

class API
{
  private $server;
  private $request;
  private $config;
  private $database;

  public function __construct(Config $config)
  {
    $this->config = $config;
    $this->database = new DB($config);;
    $this->server = $_SERVER;
    $this->request = $_REQUEST;
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
    if (!preg_match("#https?://#", $form['url'])) {
      echo '<span style="color:red">Invalid URL</span>';
      return;
    }
    $url = new Urls($this->database, new Cache($this->config));
    $url['url'] = $form['url'];
    $url->save();
    $svc = new Hasher;
    $hash = $svc->num2hash($url[Urls::PK]);
    echo "http://{$this->server['HTTP_HOST']}/{$hash}";
  }

  private function redirectAction($uri)
  {
    $url = explode('/', $uri);
    $hash = end($url);
    $svc = new Hasher;
    $urlId = $svc->hash2num($hash);
    $url = new Urls($this->database, new Cache($this->config));
    $url = $urlId ? $url->getById($urlId) : null;
    if($url) {
      header('Location: ' . $url['url'], true, 301);
    } else {
      echo "Unknown URL";
    }
  }
}
