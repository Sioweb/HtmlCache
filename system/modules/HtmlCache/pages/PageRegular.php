<?php 

class PageRegular extends \Contao\PageRegular {

  /**
   * Generate a regular page
   * @param object
   * @param boolean
   */
  public function generate($objPage, $blnCheckRequest=false) {
    parent::generate($objPage,$blnCheckRequest);
    $Data = $this->Template->parse();
    // $Data = str_replace('[[','[---[',$Data);
    $Data = $this->replaceDynamicScriptTags($Data);
    $Data = $this->minifyHtml($Data);
    
    return $Data;
  }

  /**
   * Minify the HTML markup preserving pre, script, style and textarea tags
   *
   * @param string $strHtml The HTML markup
   *
   * @return string The minified HTML markup
   */
  public function minifyHtml($strHtml)
  {
    // Split the markup based on the tags that shall be preserved
    $arrChunks = preg_split('@(</?pre[^>]*>)|(</?script[^>]*>)|(</?style[^>]*>)|(</?textarea[^>]*>)@i', $strHtml, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

    $strHtml = '';
    $blnPreserveNext = false;
    $blnOptimizeNext = false;

    // Recombine the markup
    foreach ($arrChunks as $strChunk)
    {
      if (strncasecmp($strChunk, '<pre', 4) === 0 || strncasecmp($strChunk, '<textarea', 9) === 0)
      {
        $blnPreserveNext = true;
      }
      elseif (strncasecmp($strChunk, '<script', 7) === 0 || strncasecmp($strChunk, '<style', 6) === 0)
      {
        $blnOptimizeNext = true;
      }
      elseif ($blnPreserveNext)
      {
        $blnPreserveNext = false;
      }
      elseif ($blnOptimizeNext)
      {
        $blnOptimizeNext = false;

        // Minify inline scripts
        $strChunk = str_replace(array("/* <![CDATA[ */\n", "<!--\n", "\n//-->"), array('/* <![CDATA[ */', '', ''), $strChunk);
        $strChunk = preg_replace(array('@(?<![:\'"])//(?!W3C|DTD|EN).*@', '/[ \n\t]*(;|=|\{|\}|\[|\]|&&|,|<|>|\',|",|\':|":|: |\|\|)[ \n\t]*/'), array('', '$1'), $strChunk);
        $strChunk = trim($strChunk);
      }
      else
      {
        $arrReplace = array
        (
          '/\n ?\n+/'                   => "\n",    // Convert multiple line-breaks
          '/^[\t ]+</m'                 => '<',     // Remove tag indentation
          '/>\n<(a|input|select|span)/' => '> <$1', // Remove line-breaks between tags
          '/([^>])\n/'                  => '$1 ',   // Remove line-breaks of wrapped text
          '/  +/'                       => ' ',     // Remove redundant whitespace characters
          '/\n/'                        => '',      // Remove all remaining line-breaks
          '/ <\/(div|p)>/'              => '</$1>'  // Remove spaces before closing DIV and P tags
        );

        $strChunk = str_replace("\r", '', $strChunk);
        $strChunk = preg_replace(array_keys($arrReplace), array_values($arrReplace), $strChunk);
        $strChunk = trim($strChunk);
      }

      $strHtml .= $strChunk;
    }

    return $strHtml;
  }

}