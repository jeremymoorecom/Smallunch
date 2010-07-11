<?php
class Smallunch_lib_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
  public function baseUrl($par = '')
  {
  	if ($par != '' )
  	{
  		return str_replace(array('/index.php', '/dev.php'), '', Zend_Controller_Front::getInstance()->getBaseUrl());
  	}
    return str_replace('/index.php', '', Zend_Controller_Front::getInstance()->getBaseUrl());
  }
}