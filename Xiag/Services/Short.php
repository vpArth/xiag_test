<?php
namespace Xiag\Services;

use Xiag\Core\Service;
use Xiag\Core\IService;

use Xiag\Core\Router;
use Xiag\Core\RouteData;
use Xiag\Core\ValidatorException;

use Xiag\HTML\Template;
use Xiag\HTML\Style;
use Xiag\HTML\Script;

class Short extends Service implements IService
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
      'path' => "",
      'classname' => $this,
      'method' => 'index',
      'validators' => array(),
      'responseFormat' => 'html'
    )));
  }



  public function index()
  {
    $layout = new Template(__DIR__.'/templates/layout.tpl');
    $page   = new Template(__DIR__.'/templates/short.tpl');

    $data = array();

    $styles = array();
    $styles[] = new Style('/css/style.css');
    $data['styles'] = implode('', $styles);

    $scripts = array();
    $scripts[] = new Script('/js/script.js');
    $data['scripts'] = implode('', $scripts);

    return $page->render($data, $layout);
  }

}
