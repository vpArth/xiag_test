<?php

namespace tests\Core;

use Xiag\Core\Config as C;
use Xiag\Core\DB as D;

require_once __DIR__ . "/../../Xiag/Core/Config.php";
require_once __DIR__ . "/../../Xiag/Core/DB.php";

class DB extends \PHPUnit_Framework_TestCase
{
  protected $cfg = null;
  protected $db = null;

  protected function setUp()
  {
    $this->cfg = new C(__DIR__.'/../data/db');
    $this->cfg->load('config');
    $this->db  = new D($this->cfg);

    $this->db->exec("DROP TABLE IF EXISTS `{$this->cfg['table']}`;");
    $this->db->exec("CREATE TABLE IF NOT EXISTS `{$this->cfg['table']}` (`id` int(11) NOT NULL AUTO_INCREMENT,
      `test` text NOT NULL, PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

  }

  public function testInsertSelect()
  {
    $value = 'test_value';
    $this->db->exec("INSERT INTO `{$this->cfg['table']}` (`id`, `test`) VALUES (NULL, :val);", array(':val' => $value));
    $this->assertEquals(1, $this->db->getLastId());
    $this->assertEquals($value, $this->db->cell("SELECT `test` FROM `{$this->cfg['table']}` WHERE `id` = ?", array(1)));
  }

  protected function tearDown()
  {
    $this->db->exec("DROP TABLE IF EXISTS `{$this->cfg['table']}`;");
  }
}
