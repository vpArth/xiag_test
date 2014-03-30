<?php

namespace Xiag\Tests\Core;

use Xiag\Core\Loader as L;
use Xiag\Core\LoaderException;
use Xiag\Tests\Core\Data;

require_once __DIR__ . "/../../Core/Loader.php";

class Loader extends \PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    L::getInstance()->reg();
  }

  public function testSingleton()
  {
    $loader1 = L::getInstance();
    $loader2 = L::getInstance();
    $this->assertTrue($loader1 === $loader2);
  }

  public function testSimpleClass()
  {
    $classA = new Data\A();
    $this->assertEquals($classA->foo(), 'A');
    $classB = new Data\B();
    $this->assertEquals($classB->foo(), 'B');
  }

  public function testNotFound()
  {
    try {
      new Core\Abracadabra();
      $this->assertTrue(false, 'Should be exception thrown');
    } catch (LoaderException $e) {
      $this->assertTrue(true);
    }
  }

  protected function tearDown()
  {
    L::getInstance()->unreg();
  }
}
