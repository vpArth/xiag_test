<?php

namespace xiag\tests\Core;

use Xiag\Core\Cache as C;

require_once __DIR__ . "/../../Xiag/Core/Cache.php";

class Cache extends \PHPUnit_Framework_TestCase
{
  protected $key1 = 'test_key1';
  protected $value1 = 3.14;
  protected $cache = null;
  protected function setUp()
  {
    $this->cache = new C(array('cache'=>array()));
  }

  public function testSetGet()
  {
    $this->cache->set($this->key1, $this->value1);
    $this->assertEquals($this->value1, $this->cache->get($this->key1));
  }

  public function testExpire()
  {
    $ttl = 1;
    $this->cache->set($this->key1, $this->value1, $ttl);
    sleep($ttl);
    $this->assertFalse($this->cache->get($this->key1));
  }

  public function testDelete()
  {
    $this->cache->set($this->key1, $this->value1);
    $this->cache->del($this->key1);
    $this->assertFalse($this->cache->get($this->key1));
  }


  protected function tearDown()
  {
    $this->cache->del($this->key1);
  }
}
