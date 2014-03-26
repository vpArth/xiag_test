<?php
  namespace Xiag;

  use Xiag\Core\Loader;
  use Xiag\Core\Config;
  use Xiag\Core\API;

  use Xiag\Services;

  if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"]))
    return false;    // serve the requested resource as-is.
  // \mb_internal_encoding("UTF-8");
  error_reporting(-1);

  require_once __DIR__ . "/Core/Loader.php";
  Loader::getInstance();
  $config = Config::getInstance();
  $config->load(__DIR__.'/config.local.json', Config::FORMAT_JSON);
  $config->load(__DIR__.'/config.json', Config::FORMAT_JSON);

  $api = new API();
  $api->addService(new Services\Hello());
  $api->addService(new Services\Short());
  $api->run();
