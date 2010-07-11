<?php
[[INFO]]
class [[moduleName]]_[[CONTROLLER_NAME]]Controller extends Zend_Controller_Action
{
  protected $logger;
  
  public function preDispatch()
  {
    // Set ACL Here
    require_once 'Smallunch/lib/Auth/preDispatch.php';
  }
  
  public function postDispatch()
  {
    $this->_flashMessenger->setNamespace('error');
    $this->view->errorMessages = $this->_flashMessenger->getMessages();
    $this->_flashMessenger->resetNamespace();
    $this->view->messages = $this->_flashMessenger->getMessages();
  }
  
  public function init()
  {
    $this->logger = Zend_Registry::get("logger");
    $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    $this->initView();
  }

  public function setError($message)
  {
    $this->_flashMessenger->setNamespace('error');
    $this->_flashMessenger->addMessage($message);
    $this->_flashMessenger->resetNamespace();
  }
  
  public function setMessage($message, $namespace='')
  {
    if ($namespace != '') {
      $this->_flashMessenger->setNamespace($namespace);
      $this->_flashMessenger->addMessage($message);
      $this->_flashMessenger->resetNamespace();
    }
    else
    {
      $this->_flashMessenger->addMessage($message);
    }
  }
  public function indexAction()
  {
    
  }
}