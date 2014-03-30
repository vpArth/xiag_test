<?php

namespace tests\Core;

use Xiag\Core\Config as C;

require_once __DIR__ . "/../../Xiag/Core/Config.php";

class Config extends \PHPUnit_Framework_TestCase
{
  protected $cfg = null;

  protected function setUp()
  {
    $this->cfg = new C(__DIR__.'/../data/config');
  }

  public function testSuccess()
  {
    $this->cfg->load('test');
    $this->assertEquals('test_value', $this->cfg['test_key']);
  }

  public function testNofile()
  {
    $this->cfg->load('test_not_exists');
    $this->assertTrue(true, 'File should not to be exists, all ok');
  }

  public function testMalformed()
  {
    $this->setExpectedException('Xiag\Core\ConfigException');
    $this->cfg->load('test_malformed');
    $this->fail("Expected exception not thrown");
  }

  public function testUnfnownFormat()
  {
    $this->setExpectedException('Xiag\Core\ConfigException');
    $this->cfg->load('test', 'xml');
    $this->fail("Expected exception not thrown");
  }


  protected function tearDown()
  {
  }
}
