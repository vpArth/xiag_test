<?php

namespace Xiag\Core;

/**
 * Class RouteData
 * Container for route information for Router::addRoute method
 *
 * @package Xiag\Core
 */
class RouteData
{
  public $verb;
  public $path;
  public $classname;
  public $method;
  public $validators;
  public $responseFormat = 'json';

  public function __construct($data)
  {
    $this->verb = isset($data['verb']) ? $data['verb'] : 'GET';
    $this->path = isset($data['path']) ? $data['path'] : '^/$';
    $this->classname = $data['classname'];
    $this->method = $data['method'];
    $this->validators = isset($data['validators']) ? $data['validators'] : array();
    $this->responseFormat = isset($data['responseFormat']) ? $data['responseFormat'] : '';
  }

  public function getData()
  {
    return array(
      'verb' => $this->verb,
      'path' => $this->path,
      'classname' => $this->classname,
      'method' => $this->method,
      'validators' => $this->validators,
      'responseFormat' => $this->responseFormat
    );
  }

}
