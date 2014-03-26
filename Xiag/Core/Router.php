<?php
namespace Xiag\Core;

require_once __DIR__.'/ErrorCodes.php';

class RouterException extends \Exception
{
}

class ValidatorException extends \Exception
{
}

interface IRouter
{
  public function addRoute(RouteData $data);

  public function execURI(Response $response);
}

class Router implements IRouter
{
  const VALIDATE_SUCCESS = 0;


  private static $instance = null;

  private function __clone()
  {
  }

  private function __construct()
  {
  }

  public static function getInstance()
  {
    return self::$instance ? : (self::$instance = new self());
  }

  private $routes = [];

  /**
   * Register routing rule
   *
   * @param RouteData $data
   * @return $this
   */
  public function addRoute(RouteData $data)
  {
    $route = $data->getData();
    $key = $route['verb'] . ' ' . $route['path'];
    $this->routes[$key] = $route;
    $this->responseFormat = $route['responseFormat'];
    return $this;
  }

  private $responseFormat = '';
  public function getResponseFormat()
  {
    return $this->responseFormat;
  }

  /**
   * Wrapper for user data
   *
   * @return array
   */
  protected function getParams($method)
  {
    $params = array_merge($_GET, $_POST, $_FILES);
    if (!in_array($method, array('GET', 'OPTIONS'))) {
      $type = isset($_SERVER["HTTP_CONTENT_TYPE"])?$_SERVER["HTTP_CONTENT_TYPE"]:$_SERVER["CONTENT_TYPE"];

      if ($type && $type === 'application/json') {
        $body = json_decode(file_get_contents("php://input"), 1);
        if ($body)
          $params = array_replace($params, $body);
      }
    }
    if ($token = isset($_SERVER["AUTHORIZATION"])?$_SERVER["AUTHORIZATION"]:false)
      $params['token'] = $token;
    //here can be some filters, modifications
    return $params;
  }

  /**
   * Process request
   *
   * @return mixed
   * @throws RouterException
   */
  public function execURI(Response $response)
  {
    foreach ($this->routes as $pattern => $data) {

      list($method, $path) = explode(' ', $pattern, 2);
      list($url) = explode('?', $_SERVER['REQUEST_URI'], 2);
      $matches = array();
      if ($method === $_SERVER['REQUEST_METHOD'] && preg_match('#' . $path . '#i', $url, $matches)) {
        $class = is_object($data['classname']) ? $data['classname'] : new $data['classname']();
        if (!method_exists($class, $data['method'])) {
          $classname = is_object($data['classname']) ? get_class($data['classname']) : $data['classname'];
          throw new RouterException("Method {$classname}->{$data['method']} not Exists");
        }
        $params = $this->getParams($method);
        if($matches)
          $params['__vars__'] = $matches;
        if (($error = $this->validate($data['validators'], $params)) !== self::VALIDATE_SUCCESS) {
          throw new ValidatorException($error, ErrorCodes::INVALID_PARAMS);
        }
        $result = $class->{$data['method']}($params, $response);
        if ($result !== false)
          return $result;
      }
    }
    throw new RouterException("404 Not Found");
  }

  private function validateRequired(array &$vals, array &$data)
  {
    foreach ($vals as $field)
      if (!isset($data[$field]))
        return "No required field {$field}.";
    return true;
  }

  private function validateRegular(array &$vals, array &$data)
  {
    foreach ($vals as $rules) {
      foreach ($rules as $field => $reg) {
        if (isset($data[$field])) {
          if ($reg === 'email') {
            if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL))
              return "Wrong {$field} format";
          } elseif (!preg_match($reg, $data[$field])) {
            return "Wrong {$field} format";
          }
        }
      }
    }
    return true;
  }

  /**
   * required: ['field1', 'field2']
   * regular: [['field1'=>'/^reg1$/', 'field2'=>'/^reg2$/'], ['field1'=>'/^reg3$/']]
   */
  private function validate($validators, $data)
  {
    foreach ($validators as $type => $vals) {
      switch ($type) {
        case 'required':
          $val = $this->validateRequired($vals, $data);
          if ($val !== true)
            return $val;
        break;
        case 'regular':
          $val = $this->validateRegular($vals, $data);
          if ($val !== true)
            return $val;
        break;
      }
    }
    return self::VALIDATE_SUCCESS;
  }
}
