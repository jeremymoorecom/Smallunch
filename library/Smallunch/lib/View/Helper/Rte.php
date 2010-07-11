<?php
class Smallunch_lib_View_Helper_Rte extends Zend_View_Helper_Abstract
{
  function rte($name, $value = null, $attr = array())
  {
  	#require_once 'Zend/View/Helper/FormTextarea.php';
  	#$textarea = new Zend_View_Helper_FormTextarea();
    $baseUrl = str_replace('/index.php', '', str_replace('/dev.php', '', Zend_Controller_Front::getInstance()->getBaseUrl()));
    
    $headLink = new Zend_View_Helper_HeadLink();
    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->headScript()->appendFile($baseUrl.'/js/ckeditor/ckeditor.js');
    
    $placeholder = new Zend_View_Helper_Placeholder();
    $placeholder->placeholder('jQuery')->captureStart();
    
	  echo "CKEDITOR.replace( '".$name."' );";
	  $placeholder->placeholder('jQuery')->captureEnd();
	  
	  return $this->view->formTextarea($name, $value);
  }
}