<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['default'] .= ',dynamicContent';

foreach($GLOBALS['TL_DCA']['tl_module']['palettes'] as $type => &$palette)
  $palette = preg_replace('|{title_legend},([^;]*);|','{type_legend},$1,dynamicContent;',$palette);

$GLOBALS['TL_DCA']['tl_module']['fields']['dynamicContent'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_module']['dynamicContent'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('tl_class'=>'w50 clr'),
  'sql'                     => "char(1) NOT NULL default ''"
);