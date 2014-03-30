<?php

namespace Xiag\DB;

use Xiag\Core\Cache;

class Urls extends Model
{
  const CACHE_PREFIX = 'cache_urls_';
  protected static $table = 'Urls';
  protected static $fields = array('id', 'url');
}
