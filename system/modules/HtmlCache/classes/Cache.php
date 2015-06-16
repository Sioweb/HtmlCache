<?php

/**
 * Contao Open Source CMS
 */

namespace sioweb\contao\extensions\cache;
use Contao;

/**
 * @file Cache.php
 * @class Cache
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.cache
 * @copyright Sascha Weidner, Sioweb
 */

class Cache extends \Backend {

  public function extendDCA($strName) {
    if(empty($GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback']))
      $GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback'] = array();
    $GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback'][] = array('Cache','generateSiteCache');
  }

  public function generateSiteCache() {
    $url = 'http://'.\Environment::get('host').'/cache.php';
    $data = array(
      'generate_html_cache' => 1
    );
    
    $this->curl_post_async($url,$data);
  }

  public function curl_post_async($url, $params)
  {
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
          $post_params[] = $key.'='.urlencode($val);
      }
      $post_string = implode('&', $post_params);

      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      $out = "POST ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      if (isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }
}
