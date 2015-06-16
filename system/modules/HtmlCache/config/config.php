<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file config.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.cache
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['html_cache'] = array('element'=>array(),'module'=>array());

if(TL_MODE == 'BE') {
  $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('sioweb\contao\extensions\cache\SWCache', 'extendDCA');
}

if($_POST['generate_html_cache'] == 1) {
  $GLOBALS['TL_HOOKS']['getContentElement'][] = array('sioweb\contao\extensions\cache\SWCache', 'replaceDynamicContent');
  $GLOBALS['TL_HOOKS']['getFrontendModule'][] = array('sioweb\contao\extensions\cache\SWCache', 'replaceDynamicModule');
}