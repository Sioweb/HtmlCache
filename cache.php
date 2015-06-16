<?php

$Path = pathinfo($_SERVER['SCRIPT_URI']);
if(empty($Path['basename']) || $Path['basename'] == '.html' || $Path['dirname'] == 'http:')
  $Path = array(
    'basename' => 'index.html',
    'filename' => 'index',
    'extension' => 'html'
  );

if($_POST['generate_html_cache'] == 1 || $_GET['test'] == 1) {

  define('BYPASS_TOKEN_CHECK',true);
  ignore_user_abort(true);
  set_time_limit(0);
  /**
   * Set the script name
   */
  define('TL_SCRIPT', 'index.php');

  /**
   * Initialize the system
   */
  define('TL_MODE', 'FE');
  require __DIR__ . '/system/initialize.php';

  $arrPages = array();
  $Page = \PageModel::findByHtmlCache(1);
  if(!$Page)
    return;
  while($Page->next()) {
    if($Page->type != 'regular' || !$Page->alias)
      continue;
    $arrPages[] = $Page->id;
  }

  if(!is_dir(TL_ROOT.'/system/cache/generated_html/'))
    mkdir(TL_ROOT.'/system/cache/generated_html');

  foreach($arrPages as $page_id) {
    $objPage = PageModel::findPublishedByIdOrAlias($page_id);

    if($objPage instanceof \Model\Collection)
      $objPage = $objPage->current();

    $Type = '\\'.$GLOBALS['TL_PTY'][$objPage->type];
    $PageType = new $Type();

    if(!is_bool($objPage->protected))
      $objPage->loadDetails();
    
    $File = new \File('system/cache/generated_html/'.$objPage->alias.'.html');
    // ob_start();
    // $PageType->generate($objPage,true);
    // $File->write(ob_get_contents());
    $File->write($PageType->generate($objPage,true));
    // ob_end_clean();

    $File->close();
    sleep(0.4);
  }
} elseif(is_file(dirname(__FILE__).'/system/cache/generated_html/'.$Path['basename'])) {

  /**
   * Set the script name
   */
  define('TL_SCRIPT', 'index.php');

  /**
   * Initialize the system
   */
  define('TL_MODE', 'FE');
  require __DIR__ . '/system/initialize.php';

  ob_start();
  include 'system/cache/generated_html/'.$Path['basename'];
  $content = ob_get_contents();
  ob_end_clean();

  if(strpos($content,'{{') !== false && strpos($content,'}}') !== false) {
    class htmlCache extends \Frontend {
      public function run($strContent,$alias) {
        global $objPage;

        $objPage = PageModel::findByAlias($alias);
        return $this->replaceInsertTags($strContent);
      }
    }

    $htmlCache = new htmlCache();
    $content = $htmlCache->run($content,$Path['filename']);
  }
  echo $content;
} else {
  include 'index.php';
}