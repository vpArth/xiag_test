<?php

namespace Xiag\Core;

class Service
{
  protected static function getUrlParam(array $params, $num = 0)
  {
    $num++;
    return isset($params['__vars__'], $params['__vars__'][$num])
      ? urldecode($params['__vars__'][$num])
      : false;
  }
}
