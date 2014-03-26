<?php
namespace Xiag\Services;

use Xiag\Core\Service;
use Xiag\Core\IService;

use Xiag\Core\Router;
use Xiag\Core\RouteData;

use Xiag\HTML\Template;

use Xiag\Core\ValidatorException;

class Hello extends Service implements IService
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
      'path' => "^/hello/([\S\s]+)",
      'classname' => $this,
      'method' => 'greet',
      'validators' => array(),
      'responseFormat' => 'html'
    )));
  }



  public function greet(array $params)
  {
    $layout = new Template(__DIR__.'/templates/layout.tpl');
    $page   = new Template(__DIR__.'/templates/index.tpl');
    $name = self::getUrlParam($params);

    return $page->render(array('name'=>$name), $layout);
  }

}
