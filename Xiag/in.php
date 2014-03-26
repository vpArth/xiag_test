<?php
  namespace Xiag;

  use Xiag\Core\Loader;
  use Xiag\Core\Config;

  // \mb_internal_encoding("UTF-8");
  error_reporting(-1);

  require_once __DIR__ . "/Core/Loader.php";
  Loader::getInstance();
  $config = Config::getInstance();
  $config->load(__DIR__.'/config.local.json', Config::FORMAT_JSON);
  $config->load(__DIR__.'/config.json', Config::FORMAT_JSON);
  echo $config['name'];
