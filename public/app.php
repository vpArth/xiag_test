<?php

  namespace Xiag;

  use Xiag\Core\Config;

  require_once __DIR__ . '/../loader.php';

  //for php -S
  if($_SERVER['REQUEST_URI'] === '/' || file_exists(__DIR__.$_SERVER['REQUEST_URI'])) return false;

  $config = new Config(__DIR__ . '/../config');
  $config->load('local');
  $config->load('general');

  ini_set('display_errors', $config['debug'] );
  error_reporting( $config['debug'] ? -1 : -1);

  set_exception_handler(function ($e) use ($config) {
    if($config['debug']) throw $e;
    else die("<h1>Internal Server Error</h1>");
  });

  $api = new API($config);
  $api->action();
