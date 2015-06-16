<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file Template.php
 * @class Template
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.cache
 * @copyright Sascha Weidner, Sioweb
 */

class Template extends \Contao\Template {
  public function output() {

    if (!$this->strBuffer)
      $this->strBuffer = $this->parse();

    $this->strBuffer = $this->minifyHtml($this->strBuffer);
    return $this->strBuffer;
  }
}