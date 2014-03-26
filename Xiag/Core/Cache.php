<?php

namespace Xiag\Core;

class Cache
{
  private $memcache = null;
  private $queryCount = array('get'=>0, 'set'=>0, 'del'=>0);
  private $queryTime = array('get'=>0, 'set'=>0, 'del'=>0);

  //Singleton
  private static $instance = null;
  private function __clone() {}
  private function __wakeup() {}
  public static function getInstance()
  {
    return self::$instance ? : (self::$instance = new self());
  }
  private function __construct()
  {
    if (class_exists('\Memcache')) {
      $config = Config::getInstance();
      $config = $config['cache'];
      $host = isset($config['host']) ? $config['host'] : 'localhost';
      $port = isset($config['port']) ? $config['port'] : 11211;
      $this->memcache = new \Memcache;
      $con = $this->memcache->pconnect($host, $port);
      if ($con === false) $this->memcache = null;
    }
  }

  public function getQCount() { return $this->queryCount; }
  public function getQTime() { return $this->queryTime; }

  public function set($key, $data, $time = 0)
  {
    $start = microtime(true);
    if ($this->memcache) {
      $time += rand(0, $time/2); // some cache expiring deviation 1-1.5 times
      $this->memcache->set($key, $data, false, $time);
    }
    $this->queryCount['set']++;
    $this->queryTime['set'] += microtime(true) - $start;
    return $this;
  }

  public function get($key)
  {
    $start = microtime(true);
    $res = false;
    if ($this->memcache) {
      $res = $this->memcache->get($key);
    }
    $this->queryCount['get']++;
    $this->queryTime['get'] += microtime(true) - $start;
    return $res;
  }

  public function del($key)
  {
    $start = microtime(true);
    $res = false;
    if ($this->memcache) {
      $res = $this->memcache->delete($key);
    }
    $this->queryCount['del']++;
    $this->queryTime['del'] += microtime(true) - $start;
    return $this;
  }
}
