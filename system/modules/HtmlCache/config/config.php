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

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('sioweb\contao\extensions\cache\Cache', 'extendDCA');