<?php


$_SESSION['DISABLE_CACHE'] = 1;
$Path = pathinfo($_SERVER['SCRIPT_URI']);
if(empty($Path['basename']) || $Path['basename'] == '.html' || $Path['dirname'] == 'http:' || $Path['dirname'] == 'https:') {
  $Path = array(
    'url' => '',
    'basename' => 'index.html',
    'filename' => 'index',
    'extension' => 'html'
  );
}
$Path['url'] = $_SERVER['SERVER_NAME'];

if($_POST['generate_html_cache'] == 1 || $_GET['generate_html_cache'] == 1) {
  define('BYPASS_TOKEN_CHECK',true);
  ignore_user_abort(true);
  set_time_limit(0);

  // $_SERVER['REQUEST_URI'] = 'sendeplan.html';
  /**
   * Set the script name
   */
  define('TL_SCRIPT', 'index.php');
  define('BE_USER_LOGGED_IN',false);

  /**
   * Initialize the system
   */
  define('TL_MODE', 'FE');
  require __DIR__ . '/system/initialize.php';

  $arrPages = array();
  $Page = \PageModel::findByHtmlCache(1);
  if(!$Page) {
    return;
  }

  function getRootPage($id) {
    $Page = \PageModel::findByPk($id);
    if($Page->type !== 'root') {
      $Page = getRootPage($Page->pid);
    }
    return $Page;
  }

  while($Page->next()) {
    if($Page->type != 'regular' || !$Page->alias) {
      continue;
    }
    $arrPages[] = array('id'=>$Page->id,'alias'=>$Page->alias);
  }

  if(!is_dir(TL_ROOT.'/system/cache/generated_html/')) {
    mkdir(TL_ROOT.'/system/cache/generated_html');
  }

  $files = scandir(TL_ROOT.'/system/cache/generated_html/');
  foreach($files as $file) {
    if($file === '.' || $file === '..') {
      continue;
    }
    unlink(TL_ROOT.'/system/cache/generated_html/'.$file);
  }

  foreach($arrPages as $pagedata) {
    $request = '';
    if($pagedata['alias'] !== 'index') {
      $request = $pagedata['alias'].'.html';
    }

    ClassLoader::addClasses(array
    (
        // classes
        'sioweb\contao\extensions\cache\PageRegular'     => 'system/modules/HtmlCache/pages/PageRegular.php'
    ));

    Environment::set('indexFreeRequest',$request);
    Environment::set('request',$request);
    Environment::set('requestUri',$request);

    $objPage = PageModel::findPublishedByIdOrAlias($pagedata['id']);

    if($objPage instanceof \Model\Collection) {
      $objPage = $objPage->current();
    }

    $PageType = new \sioweb\contao\extensions\cache\PageRegular();

    if(!is_bool($objPage->protected)) {
      $objPage->loadDetails();
    }

    $fileName = $objPage->alias;
    $rootPage = getRootPage($objPage->id);
    if(!empty($rootPage->dns)) {
      $fileName = $rootPage->dns.'.'.$fileName;
    }

    $File = new \File('/system/cache/generated_html/'.$fileName.'.html');

    $output = $PageType->generate($objPage,true);
    $File->write($output);

    $File->close();
    sleep(0.4);
  }
} elseif(is_file(dirname(__FILE__).'/system/cache/generated_html/'.$Path['url'].'.'.$Path['basename'])) {
  /**
   * Set the script name
   */
  define('TL_SCRIPT', 'index.php');

  /**
   * Initialize the system
   */
  define('TL_MODE', 'FE');
  require __DIR__ . '/system/initialize.php';
  // echo 'system/cache/generated_html/'.$Path['url'].'.'.$Path['basename'];

  ob_start();
  include 'system/cache/generated_html/'.$Path['url'].'.'.$Path['basename'];
  $content = ob_get_contents();
  ob_end_clean();

  class htmlCache extends \Frontend {
    public function run($strContent,$alias) {
      global $objPage;

      $objPage = PageModel::findByAlias($alias);
      // $strContent = str_replace('[---[','[[',$strContent);
      // $strContent = $this->replaceDynamicScriptTags($strContent,false);
      return $this->replaceInsertTags($strContent,false);
    }
    public function hooks($strContent) {
      if(isset($GLOBALS['TL_HOOKS']['htmlCacheOutput']) && is_array($GLOBALS['TL_HOOKS']['htmlCacheOutput']))
      {
        foreach ($GLOBALS['TL_HOOKS']['htmlCacheOutput'] as $callback)
        {
          $this->import($callback[0]);
          $strContent = $this->{$callback[0]}->{$callback[1]}($strContent, 'cache');
        }
      }
      return $strContent;
    }
  }

  $htmlCache = new htmlCache();
  if(
    (strpos($content,'&#123;&#123;') !== false && strpos($content,'&#125;&#125;') !== false) ||
    (strpos($content,'&#123;&#123;') !== false && strpos($content,'&#125;&#125;') !== false)
  ) {
    $content = $htmlCache->run($content,$Path['filename']);
  }
 
  echo $htmlCache->hooks($content);
} else {
  include 'index.php';
}