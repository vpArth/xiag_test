<?php

namespace Xiag\Core;

class Cache
{
  private $memcache = null;

  public function __construct($config)
  {
    if (class_exists('\Memcache')) {
      $config = $config['cache'];
      $host = isset($config['host']) ? $config['host'] : 'localhost';
      $port = isset($config['port']) ? $config['port'] : 11211;
      $this->memcache = new \Memcache;
      $con = $this->memcache->pconnect($host, $port);
      if ($con === false) $this->memcache = null;
    }
  }

  public function set($key, $data, $time = 0)
  {
    if ($this->memcache) {
      $time += rand(0, $time/2); // some cache expiring deviation 1-1.5 times
      $this->memcache->set($key, $data, false, $time);
    }
    return $this;
  }

  public function get($key)
  {
    $res = false;
    if ($this->memcache) {
      $res = $this->memcache->get($key);
    }
    return $res;
  }

  public function del($key)
  {
    $res = false;
    if ($this->memcache) {
      $res = $this->memcache->delete($key);
    }
    return $this;
  }
}
