<?php
  namespace Xiag;

  use Xiag\Core\Loader;
  use Xiag\Core\Config;

  if ($_SERVER['REQUEST_METHOD']=='GET' && (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|html)$/', $_SERVER["REQUEST_URI"]) || $_SERVER["REQUEST_URI"] === '/'))
    return false;

  error_reporting(-1);

  require_once __DIR__ . "/Core/Loader.php";
  Loader::getInstance();
  $config = Config::getInstance();
  $config->load(__DIR__.'/config.local.json', Config::FORMAT_JSON);
  $config->load(__DIR__.'/config.json', Config::FORMAT_JSON);

  $api = new API($_SERVER, $_REQUEST);
  $api->action();
  exit();
