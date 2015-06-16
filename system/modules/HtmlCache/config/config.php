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
if(TL_MODE == 'BE') {
  $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('sioweb\contao\extensions\cache\Cache', 'extendDCA');
  $GLOBALS['TL_HOOKS']['getContentElement'][] = array('sioweb\contao\extensions\cache\Cache', 'replaceDynamics');
}