<?php

namespace Xiag\Utils;

use Xiag\DB\Urls;
use Xiag\ISaveable;

class Hasher
{
  private $abc = "0123456789_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  //adding setter or config var for alphabet invalidate existing data... need solve this before allow different abcs
  public function num2hash($num)
  {
    if ($num <= 0) return '';
    $base = strlen($this->abc);
    return $this->abc[$num % $base] . $this->num2hash(floor($num / $base));
  }

  public function hash2num($hash)
  {
    $indexes = array_flip(str_split($this->abc));
    $num = 0;
    $base = strlen($this->abc);
    for ($i = 0,$len = strlen($hash); $i < $len; $i++) {
      if(!isset($indexes[$hash[$i]])) return false;
      $digit = $indexes[$hash[$i]];
      $num += $digit * pow($base, $i);
    }
    return $num;
  }
}
