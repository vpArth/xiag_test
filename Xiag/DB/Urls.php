<?php

namespace Xiag\DB;

use Xiag\Core\Cache;

class Urls extends Model
{
  const CACHE_PREFIX = 'cache_urls_';
  protected static $table = 'Urls';
  protected $fields = array('id', 'url');

  public static function getShort($longUrl)
  {
    $url = new static(array('url'=>$longUrl));
    $url->save();
    return 'http://'.$_SERVER['HTTP_HOST'].'/'.self::hashId($url['id']);
  }

  public static function getByUrl($url)
  {
    $key = self::CACHE_PREFIX.'_url_'.$url;
    $data = Cache::getInstance()->get($key);
    if (!$data) {
      $sql = "SELECT * FROM `".static::$table."` WHERE `url` = :url";
      $data = DB::getInstance()->row($sql, array(':url'=>$url));
      if (!$data) return false;
      Cache::getInstance()->set($key, $data);
    }
    return new static($data);
  }

  protected static $chars = array(
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '_',
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
  );

  public static function hashId($num)
  {
    if ($num <= 0) return '';
    $base = count(self::$chars);
    return (string)self::$chars[$num % $base] . self::hashId(floor($num / $base));
  }

  public static function hash2id($hash)
  {
    $indexes = array_flip(self::$chars);
    $num = 0;
    $base = count(self::$chars);
     for ($i = 0,$len = strlen($hash); $i < $len; $i++) {
      if(!isset($indexes[$hash[$i]])) return false;
      $digit = $indexes[$hash[$i]];
      $num += $digit * pow($base, $i);
    }
    return $num;
  }

  public static function getByHash($hash)
  {
    return self::getById(self::hash2id($hash));
  }
}
