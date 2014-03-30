<?php

namespace Xiag\DB;

use Xiag\Core\DB;
use Xiag\Core\Cache;
use Xiag\ISaveable;

class ModelException extends \Exception {}

abstract class Model implements \ArrayAccess
{
  const CACHE_TIME = 60;
  const CACHE_PREFIX = 'cache_users_';
  const PK = 'id';
  protected static $table = '';

  protected $data = array();

  protected static $fields = array('id');
  protected static $defaults = array('id' => null);

  public function offsetSet($offset, $value)
  {
    if (is_null($offset)) throw new ModelException('Unsupported operation');
    if (!in_array($offset, static::$fields)) throw new ModelException("Unknown field $offset");
    $this->data[$offset] = $value;
    return $this;
  }
  public function offsetExists($offset)
  {
    return isset($this->data[$offset]) || isset(static::$defaults[$offset]);
  }
  public function offsetUnset($offset)
  {
    if (!in_array($offset, static::$fields)) return $this;//throw new ModelException("Unknown field $field"); unset should be silent
    unset($this->data[$offset]);
    return $this;
  }
  public function offsetGet($offset)
  {
    return isset($this->data[$offset])
      ? $this->data[$offset]
      : (isset(static::$defaults[$offset]) ? static::$defaults[$offset] : null);
  }

  public function __get($field) { return $this->offsetGet($field); }
  public function __set($field, $value) { return $this->offsetSet($field); }
  public function __isset($field) { return $this->offsetExists($field); }
  public function __unset($field) { return $this->offsetUnset($field); }

  protected $database;
  protected $cache;
  public function __construct(DB $database, Cache $cache)
  {
    $this->database = $database;
    $this->cache = $cache;
  }
  public function setData($data)
  {
    foreach ($data as $field => $value) {
      if (in_array($field, static::$fields)) {
        $this[$field] = $value;
      }
    }
    return $this;
  }
  public function getData()
  {
    return $this->data + static::$defaults;
  }
  public function save()
  {
    $set = array();
    $cols = array();
    $vals = array();
    $data = array();
    foreach (static::$fields as $field) {
      $set[]  = "`{$field}`=:{$field}";
      $cols[] = "`{$field}`";
      $vals[] = ":{$field}";
      $data[":{$field}"] = isset($this[$field]) ? $this[$field] : null;
    }
    $set = implode(',', $set);
    $cols = implode(',', $cols);
    $vals = implode(',', $vals);

    $sql = $this->isSaved()
      ? "UPDATE `".static::$table."` SET $set WHERE `".static::PK."` = :id"
      : "INSERT INTO `".static::$table."` ({$cols}) VALUES ({$vals})";
    $this->database->exec($sql, $data);
    $pkval = $this[static::PK] = $this->database->getLastId();

    $key = static::CACHE_PREFIX.'_id_'.$pkval;
    $this->cache->set($key, $this->data, static::CACHE_TIME);

    return $pkval;
  }

  public function isSaved()
  {
    return !is_null($this[static::PK]);
  }

  public function getById($pkId)
  {
    if (!$pkId) return false;
    $key = static::CACHE_PREFIX.'_id_'.$pkId;
    $data = $this->cache->get($key);
    if (!$data) {
      $sql = "SELECT * FROM `".static::$table."` WHERE `".static::PK."` = :id";
      $data = $this->database->row($sql, array(':id'=>$pkId));
      if (!$data) return false;
      $this->cache->set($key, $data, static::CACHE_TIME); //store only fresh data
    }
    return $this->setData($data);
  }

}
