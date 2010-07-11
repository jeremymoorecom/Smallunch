<?php
class IndexController extends Zend_Controller_Action
{
  protected $logger;
  
  public function init()
  {
    $this->logger = Zend_Registry::get("logger");
  }
  
  public function __call($method, $args)
  {
    if (APPLICATION_ENV == 'development') {
      throw new Zend_Controller_Exception('Invalid method called');
    }
  }
	
  public function preDispatch()
  {
    require_once 'Smallunch/lib/resources/preDispatch.php';
  }
  
  public function indexAction()
  { 
  }
}
?>
