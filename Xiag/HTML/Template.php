<?php

namespace Xiag\HTML;

class Template
{
  protected $tpl = '';

  public function __construct($filename)
  {
    $this->tpl = file_exists($filename) ? file_get_contents($filename) : $filename;
  }

  public function render(array $data, Template $layout = null)
  {
    $html = $this->tpl;
    foreach ($data as $key => $value) {
      $html = str_replace('{{'.$key.'}}', $value, $html);
    }
    $html = preg_replace('/{{[^}]*}}/', '', $html);
    if (is_null($layout)) return $html;
    $data['child'] = $html;
    return $layout->render($data);
  }
}
