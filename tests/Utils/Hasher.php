<?php

namespace xiag\tests\Core;

use Xiag\Utils\Hasher as H;

require_once __DIR__ . "/../../Xiag/Utils/Hasher.php";

class Hasher extends \PHPUnit_Framework_TestCase
{
  protected $hasher = null;
  protected function setUp()
  {
    $this->hasher = new H();
  }

  public function testSuccess()
  {
    for($i = 0; $i< 20; $i++) {
      $id = mt_rand(1, 1000);
      $hash = $this->hasher->num2hash($id);
      $this->assertEquals($id, $this->hasher->hash2num($hash));
    }
  }

  protected function tearDown()
  {
  }
}
