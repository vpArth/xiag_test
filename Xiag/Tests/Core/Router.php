<?php

namespace Xiag\Tests\Core;

use Xiag\Core\Router as R;
use Xiag\Core\RouteData;
use Xiag\Core\RouterException;
use Xiag\Core\ValidatorException;

require_once __DIR__ . "/../../Core/Router.php";
require_once __DIR__ . "/../../Core/RouteData.php";

class Router extends \PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $_GET = array();
    $_POST = array();
    $_FILES = array();
  }

  public function action($data)
  {
    return $data;
  }

  public function testSuccess()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/testuri\d+$",
      'classname' => $this,
      'method' => 'action'
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/testuri25?somequery=5';
    $_GET = ['somequery' => 5];
    $result = $router->execURI();
    $this->assertEquals($result['somequery'], 5);
  }

  public function testFails()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/testuri\d+$",
      'classname' => $this,
      'method' => 'wrong'
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test?somequery=5';
    $_GET = ['somequery' => 5];
    try {
      $router->execURI();
      $this->assertTrue(false, "Should thrown RouterException");
    } catch (RouterException $e) {
      $this->assertEquals($e->getMessage(), "Unregistered route");
    }
    $_SERVER['REQUEST_URI'] = '/testuri1';
    try {
      $router->execURI();
      $this->assertTrue(false, "Should thrown RouterException");
    } catch (RouterException $e) {
      $this->assertEquals($e->getMessage(), "Method " . get_class() . "->wrong not Exists");
    }

  }

  public function testValReqOk()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        'required' => array('reqparam')
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test?reqparam=5';
    $_GET = ['reqparam' => 5];
    $result = $router->execURI();
    $this->assertEquals($result['reqparam'], 5);
  }

  public function testValReqOk2()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        'required' => array('reqparam')
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test?reqparam=5';
    $_GET = ['reqparam' => 5];
    $result = $router->execURI();
    $this->assertEquals($result['reqparam'], 5);
  }

  public function testValReqFail()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        'required' => array('reqparam')
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test?param=5';
    $_GET = ['param' => 5];
    try {
      $result = $router->execURI();
      $this->assertEquals($result['param'], 5);
    } catch (ValidatorException $e) {
      $this->assertEquals($e->getMessage(), "No required field reqparam.");
    }
  }

  public function testValRegularOk()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        // 'regular' => array(array('email'=>'email'), array('email'=>'/^[\s\S]{,10}$/')),
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $_SERVER['REQUEST_URI'] = '/test?email=arth.inbox+test@gmail.com&test=3';
    $_GET = ['email' => 'arth.inbox+test@gmail.com', 'test' => 3];
    $result = $router->execURI();
    $this->assertEquals($result['test'], 3);
  }

  public function testValRegularIsNotRequired()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        // 'regular' => array(array('email'=>'email'), array('email'=>'/^[\s\S]{,10}$/')),
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';

    // regular constraints are not required constraints
    $_SERVER['REQUEST_URI'] = '/test?test=5';
    $_GET = ['test' => 5];
    $result = $router->execURI();
    $this->assertEquals($result['test'], 5);
  }

  public function testValRegularEmail()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        'regular' => array(array('email'=>'email'), array('email'=>'/^[\s\S]{,10}$/')),
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';

    //wrong email
    $_SERVER['REQUEST_URI'] = '/test?test=3&email=wrong@email@mail.ru';
    $_GET = ['email' => 'wrong@email@mail.ru', 'test' => 3];
    try {
      $result = $router->execURI();
      $this->assertEquals($result['test'], 3);
    } catch (ValidatorException $e) {
      $this->assertEquals($e->getMessage(), "Wrong email format");
    }
  }

  public function testValRegularExpression()
  {
    $router = R::getInstance();
    $router->addRoute(new RouteData(array(
      'verb' => 'GET',
      'path' => "^/test$",
      'classname' => $this,
      'method' => 'action',
      'validators' => array(
        'regular' => array(array('email'=>'email'), array('email'=>'/^[\s\S]{,10}$/')),
      ),
    )));
    $_SERVER['REQUEST_METHOD'] = 'GET';

    //long email
    $_SERVER['REQUEST_URI'] = '/test?test=3&email=very_very_very_very_very_very_very_long_email@mail.ru';
    $_GET = ['test' => 3, 'email' => 'very_very_very_very_very_very_very_long_email@mail.ru'];
    try {
      $result = $router->execURI();
      $this->assertEquals($result['test'], 3);
    } catch (ValidatorException $e) {
      $this->assertEquals($e->getMessage(), "Wrong email format");
    }

  }

  protected function tearDown()
  {
  }
}
