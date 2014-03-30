<?php

namespace xiag\tests\Core;

use Xiag\Core\Cache as C;

require_once __DIR__ . "/../../Xiag/Core/Cache.php";

class Cache extends \PHPUnit_Framework_TestCase
{
  protected $key1 = 'test_key1';

  protected $cache = null;
  protected function setUp()
  {
    $this->cache = new C(array('cache'=>array()));
  }

  public function testSetGet()
  {
    $value = 3.14;
    $this->cache->set($this->key1, $value);
    $this->assertEquals($value, $this->cache->get($this->key1));
  }

  public function testExpire()
  {
    $value = 3.14;
    $ttl = 1;
    $this->cache->set($this->key1, $value, $ttl);
    sleep($ttl);
    $this->assertFalse($this->cache->get($this->key1));
  }

  public function testDelete()
  {
    $value = 3.14;
    $this->cache->set($this->key1, $value);
    $this->cache->del($this->key1);
    $this->assertFalse($this->cache->get($this->key1));
  }


  protected function tearDown()
  {
    $this->cache->del($this->key1);
  }
}
