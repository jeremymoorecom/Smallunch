<?php
class Smallunch_lib_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
  /**
   * Returns base url for application.
   * If $par is provided, then index.php / dev.php is removed from base url (used for linking to image/js/css files)
   * 
   */
  public function baseUrl($par = '')
  {
  	if ($par != '' )
  	{
  		return str_replace(array('/index.php', '/dev.php'), '', Zend_Controller_Front::getInstance()->getBaseUrl());
  	}
    return str_replace('/index.php', '', Zend_Controller_Front::getInstance()->getBaseUrl());
  }
}