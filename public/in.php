<?php
  namespace Xiag;

  use Xiag\Core\Config;

  if ($_SERVER['REQUEST_METHOD']=='GET' && (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|html)$/', $_SERVER["REQUEST_URI"]) || $_SERVER["REQUEST_URI"] === '/'))
    return false;

  error_reporting(-1);

  require_once __DIR__ . '/../loader.php';

  $config = Config::getInstance();
  $config->load(__DIR__.'/../config/config.local.json', Config::FORMAT_JSON);
  $config->load(__DIR__.'/../config/config.json', Config::FORMAT_JSON);

  ini_set('display_errors', $config['debug'] );
  error_reporting( $config['debug'] ? -1 : -1);

  set_exception_handler(function ($e) use ($config) {
    if($config['debug']) throw $e;
    else die("<h1>Internal Server Error</h1>");
  });


  $api = new API($_SERVER, $_REQUEST);
  $api->action();
  exit();
