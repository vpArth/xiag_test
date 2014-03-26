<?php

namespace Xiag\Utils;

use Xiag\DB\Urls;

class UrlShotener
{
  private $url;
  public function __construct($url)
  {
    $this->url = self::generate($url);
  }

  protected static function generate($url)
  {
    return Urls::getShort($url);
  }

  public function getUrl()
  {
    return $this->url;
  }
}
