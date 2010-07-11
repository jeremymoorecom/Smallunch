<?php
/**
 * Add specified javascript & css files from assets.ini to head
 * 
 * @author Jeremy Moore
 *
 */ 
class Smallunch_lib_plugins_Layout extends Zend_Controller_Plugin_Abstract
{
	// keep track if we've already added favion
	protected $favicon = false;
	
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    $config = new Zend_Config_Ini(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'assets.ini', 'general');
    
    $stylesheet_dir = $config->style->stylesheet_dir;
    $js_dir = $config->js->js_dir;
    $yui_dir = '';
    if ($config->yui) {
      $yui_dir = $config->yui->dir;
    }
    $baseUrl = str_replace('index.php', '', str_replace('dev.php', '', Zend_Controller_Front::getInstance()->getBaseUrl()));
        
    if (substr($baseUrl, -1, 1) == '/') {
      $baseUrl = substr($baseUrl, 0, strlen($baseUrl) -1);
    }
    require_once 'Zend/View/Helper/HeadLink.php';
    $head = new Zend_View_Helper_HeadLink();
    // favicon
    if (isset($config->favicon) && $config->favicon != '' && $this->favicon == false) {
      $head->headLink(array('rel' => 'shortcut icon', 'href' => $baseUrl.DIRECTORY_SEPARATOR.$config->favicon));
      $this->favicon = true;
    }
    
    if ($config->style->blueprintcss == true) {
      $head->headLink()
      ->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.$config->style->blueprintcss_dir.DIRECTORY_SEPARATOR.'blueprint'.DIRECTORY_SEPARATOR.'screen.css', 'screen')
      ->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'blueprint'.DIRECTORY_SEPARATOR.'print.css', 'print')
      ->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'blueprint'.DIRECTORY_SEPARATOR.'ie.css', 'screen print', 'IE')
      ;
    }
    
    
    // Add general section css & js to head.
    $this->addToHead($config, $stylesheet_dir, $js_dir, $head, $baseUrl, $yui_dir);
    // pull entire config file (error when trying to pull module name sectio & does not exist
    $config = new Zend_Config_Ini(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'assets.ini');
    $req = Zend_Controller_Front::getInstance()->getRequest();
    $mod = $req->getModuleName().'.'.strtolower($req->getControllerName()).'.'.$req->getActionName();
    
    // if module name has extra js or css, add to head
    if (isset($config->$mod))
    {
      if (!empty($config->$mod)) {
      $this->addToHead($config->$mod, $stylesheet_dir, $js_dir, $head, $baseUrl, $yui_dir);
      }
    }
  }
  
  private function addToHead($config, $stylesheet_dir = '', $js_dir = '', $head, $baseUrl, $yui_dir = '')
  {
  	// Add CSS
    if (isset($config->style->screen->stylesheet) && $config->style->screen->stylesheet != '') {
      $css = explode(',', $config->style->screen->stylesheet);
      foreach ($css as $s) {
        if (substr_compare(trim($s), 'http', 0, 4) == 0) {
          $head->headLink()->appendStylesheet(trim($s), 'screen');
        }
        else 
        {
          $head->headLink()->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.$stylesheet_dir.DIRECTORY_SEPARATOR.trim($s).'.css', 'screen');
        }
      }
    }
    
    if (isset($config->style->print->stylesheet) && $config->style->print->stylesheet != '') {
      $css = explode(',', $config->style->print->stylesheet);
      foreach ($css as $s) {
        if (substr_compare(trim($s), 'http', 0, 4) == 0) {
          $head->headLink()->appendStylesheet(trim($s), 'print');
        }
        else 
        {
          $head->headLink()->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.$stylesheet_dir.DIRECTORY_SEPARATOR.trim($s).'.css', 'print');
        }
      }
    }
    // yui
    if (isset($config->yui->css) && $config->yui->css != '') {
      $css = explode(',', $config->yui->css);
      foreach ($css as $s) {
        if (substr_compare(trim($s), 'http', 0, 4) == 0) {
          $head->headLink()->appendStylesheet(trim($s), 'screen');
        }
        else 
        {
          $head->headLink()->appendStylesheet($baseUrl.DIRECTORY_SEPARATOR.$yui_dir.DIRECTORY_SEPARATOR.trim($s).'.css', 'screen');
        }
      }
    }
    // Add JS
    $headScript = new Zend_View_Helper_HeadScript();
    
    if (isset($config->js->js) && $config->js->js != '') {
      $js = explode(',', $config->js->js);
      
      foreach ($js as $j) {
        if (substr_compare(trim($j), 'http', 0, 4) == 0) {
          $headScript->headScript()->appendFile(trim($j));
        }
        else {
          $headScript->headScript()->appendFile($baseUrl.DIRECTORY_SEPARATOR.$js_dir.DIRECTORY_SEPARATOR.trim($j).'.js');
        }
      }
    }
    if (isset($config->js->prepend) && $config->js->prepend != '') {
      $js = array_reverse(explode(',', $config->js->prepend));
      
      foreach ($js as $j) {
        if (substr_compare(trim($j), 'http', 0, 4) == 0) {
          $headScript->headScript()->prependFile(trim($j));
        }
        else {
          $headScript->headScript()->prependFile($baseUrl.DIRECTORY_SEPARATOR.$js_dir.DIRECTORY_SEPARATOR.trim($j).'.js');
        }
      }
    }
    // yui
    if (isset($config->yui->js) && $config->yui->js != '') {
      $js = explode(',', $config->yui->js);
      
      foreach ($js as $j) {
        if (substr_compare(trim($j), 'http', 0, 4) == 0) {
          $headScript->headScript()->appendFile(trim($j));
        }
        else {
          $headScript->headScript()->appendFile($baseUrl.DIRECTORY_SEPARATOR.$yui_dir.DIRECTORY_SEPARATOR.trim($j).'.js');
        }
      }
    }
  }
} 