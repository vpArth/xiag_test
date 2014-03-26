<?php
namespace Xiag\Core;

class Config implements \ArrayAccess
{
  const FORMAT_JSON = 'json';
  //Singleton
  private static $instance = null;
  private function __clone() {}
  private function __wakeup() {}
  public static function getInstance()
  {
    return self::$instance ? : (self::$instance = new self());
  }
  private function __construct() {}

  //ArrayAccess
  private $data = array();
  public function offsetSet($offset, $value) { if (is_null($offset)) $this->data[] = $value; else $this->data[$offset] = $value; }
  public function offsetExists($offset) { return isset($this->data[$offset]); }
  public function offsetUnset($offset) { unset($this->data[$offset]); }
  public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }

  public function loadJSON($filename)
  {
    if (!file_exists($filename)) {
      // throw new \Exception("Config file $filename not found");
      return $this;
    }
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
    if ($data === false || !is_array($data)) {
      throw new \Exception("Wrong config json format");
    }
    $this->data += $data; // Don't overrides, need load configs from local to global
    return $this;
  }

  public function load($filename, $format = self::FORMAT_JSON)
  {
    switch ($format) {
      case 'json': return $this->loadJSON($filename);
      default: throw new \Exception('Unknown config format');
    }
  }

}
