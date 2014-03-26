<?php

namespace Xiag\HTML;

class Script
{
  private $src;

  public function __construct($src)
  {
    $this->src = $src;
  }

  public function render()
  {
    return '<script src="'.$this->src.'" ></script>';
  }

  public function __toString()
  {
    return $this->render();
  }
}
