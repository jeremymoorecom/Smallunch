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
	
  public function indexAction()
  { 
  }

  public function page_not_foundAction() {
    $this->render('404');
  }
}
?>
