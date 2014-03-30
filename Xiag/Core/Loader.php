<?php
namespace Xiag\Core;

class LoaderException extends \Exception
{
}

class Loader
{
  //Singleton
  private static $instance = null;

  private function __clone()
  {
  }

  private function __wakeup()
  {
  }

  private function __construct()
  {
    $this->reg();
  }

  public function reg()
  {
    spl_autoload_register(array($this, 'autoLoadClass'), true, false);
  }

  public function unreg()
  {
    spl_autoload_unregister(array($this, 'autoLoadClass'));
  }

  public static function getInstance()
  {
    return self::$instance ? : (self::$instance = new self());
  }

  /**
   * Loads $file php code
   *
   * @param $file
   * @param array $vars
   * @return $this
   */
  public function load($file, array $vars = null)
  {
    if ($vars !== null) extract($vars);
    if (!is_file($file)) eval(" ?>$file<?php ");
    else require_once($file);
    return $this;
  }

  /**
   * Autoload callback
   *
   * @param $class
   * @param null $path
   * @param bool $isAuto
   * @return bool
   * @throws LoaderException
   */
  protected function autoLoadClass($class, $path = null)
  {
    $class = strtolower($class);
    if ($class[0] != '\\') $class = '\\' . $class;
    $file = $this->searchFile($class, $path);
    if ($file === false) throw new LoaderException($class . ' Not Found');
    return true;
  }

  /**
   * Recursive class search in filesystem
   *
   * @param $class
   * @param null $path
   * @return bool
   */
  protected function searchFile($class, $path = null)
  {
    $path = $path ? : realpath($_SERVER['DOCUMENT_ROOT']);

    $files = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($path),
      \RecursiveIteratorIterator::SELF_FIRST);

    if (!$files->valid()) return false;

    foreach ($files as $file) {
      if (!is_file($file)) continue;
      $base = basename($file);
      if (!preg_match('/\.php$/i', $base)) continue;
      $ns = '';
      $tokens = token_get_all(file_get_contents($file));
      foreach ($tokens as $n => $token) {
        switch ($token[0]) {
          case T_NAMESPACE:
            $ns = $this->getNS($tokens, $n) . '\\';
            break;
          case T_CLASS:
          case T_INTERFACE:
            $classname = $this->getClassName($tokens, $n);
            // echo "\n".'\\' . $ns . $classname  .  "   " .$class."\n";
            if (strcasecmp('\\' . $ns . $classname, $class) == 0) {
              $this->load($file);
              break 3;
            }
        }
      }
    }
    return class_exists($class) || interface_exists($class) ? $file : false;
  }

  /**
   * Parse namespace
   *
   * @param array $tokens
   * @param $n
   * @return string
   */
  protected function getNS(array $tokens, $index)
  {
    $namespace = '';
    do {
      $token = $tokens[++$index];
      if ($token[0] == T_STRING || $token[0] == T_NS_SEPARATOR)
        $namespace .= $token[1];
    } while ($token != ';');
    return $namespace;
  }

  /**
   * Parse classname
   *
   * @param array $tokens
   * @param $n
   * @return mixed
   */
  protected function getClassName(array $tokens, $index)
  {
    do {
      $token = $tokens[++$index];
    } while ($token[0] != T_STRING);
    return $token[1];
  }
}
