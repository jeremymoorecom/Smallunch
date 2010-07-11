<?php
class Error_ErrorController extends Zend_Controller_Action
{
	public function errorAction()
	{
		if (APPLICATION_ENV == 'development') {
      echo "<pre>";print_r($this->_getParam('error_handler')->exception->getTrace());echo "</pre>";
      throw new Exception($this->_getParam('error_handler')->exception->getMessage().'<pre>'.print_r($this->_getParam('error_handler')->exception->getTrace(), true).'</pre>');
    }
  }
}