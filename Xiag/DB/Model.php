<?php

namespace Xiag\DB;

use Xiag\Core\DB;
use Xiag\Core\Cache;

abstract class Model implements \ArrayAccess
{
  const CACHE_TIME = 60;
  const CACHE_PREFIX = 'cache_users_';
  protected static $table = '';

  protected $data = array();
  protected $fields = array();

  public function offsetSet($offset, $value) { if (is_null($offset)) $this->data[] = $value; else $this->data[$offset] = $value; }
  public function offsetExists($offset) { return isset($this->data[$offset]); }
  public function offsetUnset($offset) { unset($this->data[$offset]); }
  public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }

  public function __construct($data)
  {
    $this->setData($data);
  }
  public function setData($data)
  {
    $this->data = $data;
    return $this;
  }
  public function getData()
  {
    return $this->data;
  }
  public function save()
  {
    $set = array();
    $cols = array();
    $vals = array();
    $data = array();
    foreach ($this->fields as $field) {
      $set[]  = "`{$field}`=:{$field}";
      $cols[] = "`{$field}`";
      $vals[] = ":{$field}";
      $data[":{$field}"] = isset($this->data[$field]) ? $this->data[$field] : null;
    }
    $set = implode(',', $set);
    $cols = implode(',', $cols);
    $vals = implode(',', $vals);

    $sql = isset($this->data['id'])
      ? "UPDATE `".static::$table."` SET $set WHERE `id` = :id"
      : "INSERT INTO `".static::$table."` ({$cols}) VALUES ({$vals})";
    $dbh = DB::getInstance();
    $dbh->exec($sql, $data);
    if(!isset($this->data['id'])) $this->data['id'] = $dbh->getLastId();

    $key = static::CACHE_PREFIX.'_id_'.$this->data['id'];
    Cache::getInstance()->set($key, $this->data, static::CACHE_TIME);

    return $this->data['id'];
  }

  public static function getById($pkId)
  {
    if (!$pkId) return false;
    $key = static::CACHE_PREFIX.'_id_'.$pkId;
    $data = Cache::getInstance()->get($key);
    if (!$data) {
      $sql = "SELECT * FROM `".static::$table."` WHERE `id` = :id";
      $data = DB::getInstance()->row($sql, array(':id'=>$pkId));
      if (!$data) return false;
      Cache::getInstance()->set($key, $data, self::CACHE_TIME); //store only fresh data
    }
    return new static($data);
  }

}
