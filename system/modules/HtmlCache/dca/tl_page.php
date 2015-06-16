<?php

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace(',includeCache',',includeCache,htmlCache',$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']);

$GLOBALS['TL_DCA']['tl_page']['fields']['htmlCache'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_page']['htmlCache'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array(),
  'sql'                     => "char(1) NOT NULL default ''"
);