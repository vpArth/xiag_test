<?php

namespace Xiag\Utils;

use Xiag\DB\Urls;
use Xiag\ISaveable;

class Hasher
{
  private $abc = "0123456789_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  public function setAlphabet($abc)
  {
    //abc should not contain duplicates;
    $this->abc = $abc;
  }

  private function num2hash($num)
  {
    if ($num <= 0) return '';
    $base = strlen($this->abc);
    return $this->abc[$num % $base] . $this->num2hash(floor($num / $base));
  }

  public function genHash(ISaveable $model)
  {
    if (!$model->isSaved())
      $model->save();
    $num = $model['id'];
    return $this->num2hash($num);
  }

  public function hash2num($hash)
  {
    $indexes = array_flip(str_split($this->abc));
    $num = 0;
    $base = strlen($this->abc);
    // if (count($indexes)!==$base) "Wrong alphabet :( There're duplicates";
    for ($i = 0,$len = strlen($hash); $i < $len; $i++) {
      if(!isset($indexes[$hash[$i]])) return false;
      $digit = $indexes[$hash[$i]];
      $num += $digit * pow($base, $i);
    }
    return $num;
  }
}
