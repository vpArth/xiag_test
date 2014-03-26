<?php
namespace Xiag\Services;

use Xiag\Core\IService;

use Xiag\Core\Router;
use Xiag\Core\RouteData;

use Xiag\Core\ValidatorException;

class Hello implements IService
{
  public function __construct()
  {
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
      'path' => "^/hello",
      'classname' => get_class($this),
      'method' => 'hello',
      'validators' => array(),
      'responseFormat' => 'html'
    )));
  }



  public function hello()
  {
    return '<h1>Hello, world</h1>';
  }

}
