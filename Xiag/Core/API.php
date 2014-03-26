<?php
namespace Xiag\Core;

class API
{

  const BEFORE_RESPONSE = 1;

  private $start;
  private $router = null;
  private $response = null;

  public function __construct()
  {
    $this->start = microtime(true);
    $this->router = Router::getInstance();
    $this->response = new Response();
  }

  /**
   * Register service routing to API
   *
   * @param SDK\IService $svc
   * @return $this
   */
  public function addService(IService $svc)
  {
    $svc->registerRoutes($this->router);
    return $this;
  }
  public function getResponse()
  {
    return $this->response();
  }

  private $callbacks = array(self::BEFORE_RESPONSE => array());
  public function registerCallback($callback, $type = self::BEFORE_RESPONSE)
  {
    if (!is_array($this->callbacks[$type])) return;
    $this->callbacks[$type][] = $callback;
  }
  public function runCallbacks($type = self::BEFORE_RESPONSE, &$param)
  {
    if (!is_array($this->callbacks[$type])) return;
    foreach ($this->callbacks[$type] as $callback) {
      if(is_callable($callback))
        $callback($param);
    }
  }

  /**
   * Process API request
   *
   */
  public function run()
  {
    try {
      $result = $this->router->execURI();
    } catch (\Exception $e) {
      $result = 'Something wrong happened';
    }
    $this->runCallbacks(self::BEFORE_RESPONSE, $result);
    if(isset($e)) echo $e;
    $this->response->send($result, $this->router->getResponseFormat());
  }
}
