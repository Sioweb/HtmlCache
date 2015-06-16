<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['default'] .= ',dynamicContent';

foreach($GLOBALS['TL_DCA']['tl_content']['palettes'] as $type => &$palette)
  $palette = preg_replace('|{type_legend},([^;]*);|','{type_legend},$1,dynamicContent;',$palette);

$GLOBALS['TL_DCA']['tl_content']['fields']['dynamicContent'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['dynamicContent'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'sql'                     => "char(1) NOT NULL default ''"
);