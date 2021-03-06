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
if(!class_exists('SWCache')) {
class SWCache extends \Backend {

  public function extendDCA($strName) {
    if(empty($GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback']))
      $GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback'] = array();
    $GLOBALS['TL_DCA'][$strName]['config']['onsubmit_callback'][] = array('SWCache','generateSiteCache');
  }

  public function generateSiteCache() {
    $url = 'http'.(\Environment::get('ssl')?'s':'').'://'.\Environment::get('host').'/cache.php';
    $data = array(
      'generate_html_cache' => 1
    );
    
    $this->curl_post_async($url,$data);
  }

  public function replaceDynamicContent($objRow, $strBuffer, $objModule) {
    if(TL_MODE == 'BE')
      return $strBuffer;

    if($objRow->dynamicContent)
      return '{{insert_content::'.$objRow->id.'}}';

    return $strBuffer;
  }

  public function replaceDynamicModule($objRow, $strBuffer, $objModule) {
    if(TL_MODE == 'BE')
      return $strBuffer;

    if($objRow->dynamicContent)
      return '{{insert_module::'.$objRow->id.'}}';

    return $strBuffer;
  }

  public function curl_post_async($url, $params)
  {
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
          $post_params[] = $key.'='.urlencode($val);
      }
      $post_string = implode('&', $post_params);

      $parts=parse_url($url);

      if(empty($parts['port'])) {
        $parts['port'] = 80;
      }

      if($parts['scheme'] === 'https') {
        $parts['port'] = 443;
        if(strpos($parts['host'],'ssl://') === false) {
          $fp = fsockopen('ssl://'.$parts['host'],$parts['port'],$errno,$errstr,30);
        }
      } else {
          $fp = fsockopen('ssl://'.$parts['host'],$parts['port'],$errno,$errstr,30);
      }

      $out = "POST ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      if (isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);

      // echo $out;
      // echo '<br><br>';

      // while (!feof($fp)) {
      //     echo fgets($fp, 128);
      // }
      // die();
      fclose($fp);
  }
}}
