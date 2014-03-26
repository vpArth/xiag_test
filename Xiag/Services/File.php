<?php
namespace Xiag\Services;

use Xiag\Core\IService;

use Xiag\Core\Router;
use Xiag\Core\RouteData;

use Xiag\Core\ValidatorException;

class File implements IService
{
  private $base;
  private $extensions = array('ico');
  public function __construct($base = null, $extensions = null)
  {
    $this->base = $base ?: __DIR__.'/..';
    if($extensions) $this->extensions = $extensions;
  }

  /**
   * Register game required routes to provided router
   *
   * @param Core\Router $router
   */
  public function registerRoutes(Router $router)
  {
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/([\s\S]+)",
      'classname' => get_class($this),
      'method' => 'get',
      'validators' => array(),
      'responseFormat' => 'html'
    )));
  }



  public function get(array $params)
  {
    $filename = $this->base . '/' . $params['__vars__'][1];
    $ext = explode('.', $filename);
    $ext = end($ext);
    if (file_exists($filename) && in_array($ext, $this->extensions))
      return file_get_contents($filename);
    return false;
  }

}
