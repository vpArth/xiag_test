<?php
  namespace Xiag;

  use Xiag\Core\Loader;
  use Xiag\Core\Config;
  use Xiag\Core\API;

  use Xiag\Services;

  // \mb_internal_encoding("UTF-8");
  error_reporting(-1);

  require_once __DIR__ . "/Core/Loader.php";
  Loader::getInstance();
  $config = Config::getInstance();
  $config->load(__DIR__.'/config.local.json', Config::FORMAT_JSON);
  $config->load(__DIR__.'/config.json', Config::FORMAT_JSON);

  $api = new API();
  $api->addService(new Services\File(__DIR__));
  $api->addService(new Services\Hello());
  $api->run();
