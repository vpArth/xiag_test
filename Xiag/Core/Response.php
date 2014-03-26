<?php
namespace Xiag\Core;

class Response
{
  private $config = null;
  /**
   * Class for http response
   * Masks all unwanted echos
   *
   * @param string $type
   */
  public function __construct()
  {
    $this->config = Config::getInstance();
    ob_start(function ($output) {
      $this->debug($output);
      return '';
    });
  }

  private $headers = array();
  public function header($name, $value)
  {
    $this->headers[$name] = $value;
  }
  private function headers()
  {
    foreach ($this->headers as $name => $value)
      header("$name: $value");
  }


  /**
   * Process response data
   *
   * @param array $data
   */
  public function send($data, $format)
  {
    ob_end_flush();
    switch ($format) {
      case 'json':
        $this->sendJSON($data);
        break;
      case 'html':
        $this->sendHTML($data);
        break;
      default:
        $this->defaultSend($data);
    }
  }

  /**
   * Default response implementation
   *
   * @param array $data
   */
  private function defaultSend($data)
  {
    $this->headers();
    echo $data;
  }

  /**
   * JSON response implementation
   *
   * @param array $data
   */
  private function sendJSON(array $data)
  {
    $json = json_encode($data);
    $this->header('Content-Type', 'application/json');
    $this->header('Content-Size', strlen($json));
    $this->headers();
    echo $json;
  }

  /**
   * HTML response implementation
   *
   * @param string $html
   */
  private function sendHTML($html)
  {
    $this->header('Content-Type', 'text/html');
    $this->header('Content-Size', strlen($html));
    $this->headers();
    echo $html;
  }

  /**
   * Callback to store all undirect output to a file(last request only)
   *
   * @param $data
   */
  private function debug($data)
  {
    file_put_contents('debug.log', $data);
  }
}
