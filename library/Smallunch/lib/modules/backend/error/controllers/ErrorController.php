<?php
Zend_Loader::loadClass('Smallunch_lib_bundles_ExceptionFormatter_ExceptionFormatter');
class Error_ErrorController extends Zend_Controller_Action
{
  public function errorAction()
  {
    if (APPLICATION_ENV == 'development') {
      Smallunch_lib_bundles_ExceptionFormatter_ExceptionFormatter::display(
        $this->_getParam('error_handler'),
        $this->_helper,
        true
      );
    }
  }
}