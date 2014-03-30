<?php

namespace Xiag\DB;

use Xiag\Core\DB;
use Xiag\Core\Cache;
use Xiag\ISaveable;

abstract class Model implements \ArrayAccess, ISaveable
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

  protected $db;
  protected $cache;
  public function __construct($data, DB $db, Cache $cache)
  {
    $this->db = $db;
    $this->cache = $cache;
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
    $this->db->exec($sql, $data);
    if(!isset($this->data['id'])) $this->data['id'] = $this->db->getLastId();

    $key = self::CACHE_PREFIX.'_id_'.$this->data['id'];
    $this->cache->set($key, $this->data, static::CACHE_TIME);

    return $this->data['id'];
  }

  public function isSaved()
  {
    return isset($this['id']);
  }

  public function getById($pkId)
  {
    if (!$pkId) return false;
    $key = self::CACHE_PREFIX.'_id_'.$pkId;
    $data = $this->cache->get($key);
    if (!$data) {
      $sql = "SELECT * FROM `".static::$table."` WHERE `id` = :id";
      $data = $this->db->row($sql, array(':id'=>$pkId));
      if (!$data) return false;
      $this->cache->set($key, $data, self::CACHE_TIME); //store only fresh data
    }
    return $this->setData($data);
  }

}
