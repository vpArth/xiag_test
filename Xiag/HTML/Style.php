<?php

namespace Xiag\HTML;

class Style
{
  private $href;

  public function __construct($href)
  {
    $this->href = $href;
  }

  public function render()
  {
    return '<link href="'.$this->href.'" rel="stylesheet" />';
  }

  public function __toString()
  {
    return $this->render();
  }
}
