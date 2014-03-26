<?php
  namespace Xiag;

  use Xiag\Core\Loader;

  // \mb_internal_encoding("UTF-8");
  error_reporting(-1);

  require_once __DIR__ . "/Core/Loader.php";
  Loader::getInstance();

  echo "Hello, world";
