<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file autoload.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.cache
 * @copyright Sascha Weidner, Sioweb
 */

ClassLoader::addNamespaces(array
(
    'sioweb\contao\extensions\cache'
));
/*
 * Namespace\Klassenname => Pfad/zum/Klassenname 
 * Die Klassen werden schönerweise in Modules / Widgets / Classes / Elements etc gruppiert
 */
ClassLoader::addClasses(array
(
    // classes
    'sioweb\contao\extensions\cache\SWCache'     => 'system/modules/HtmlCache/classes/SWCache.php',
));

if($_POST['generate_html_cache'] == 1 || $_GET['test'] == 1) {
  ClassLoader::addClasses(array
  (
    'PageRegular'  => 'system/modules/HtmlCache/pages/PageRegular.php',
    'Template'     => 'system/modules/HtmlCache/library/sioweb/Template.php',
  ));
}

/* Templatename => Pfad zu den Templates */
TemplateLoader::addFiles(array
(
    // 'mod_dummy'   => 'system/modules/dummy/templates',
    // 'be_dummy'    => 'system/modules/dummy/templates/be',
));