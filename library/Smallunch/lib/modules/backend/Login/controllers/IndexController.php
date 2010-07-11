<?php
class Login_IndexController extends Zend_Controller_Action
{
  function preDispatch()
  {
    
  }
  function indexAction()
  {
  	$this->_forward('login');
  }
  function loginAction() {  
    if ($this->_request->isPost())
    {
      // collect the data from the user
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $f = new Zend_Filter_StripTags();
      $username = $f->filter($this->_request->getPost('username'));
      $password = $f->filter($this->_request->getPost('password'));
      if (empty($username))
      {
        $this->view->message = 'Please provide a username.';
      }
      elseif (empty($password))
      {
        $this->view->message = 'Please enter a password.';
      }
      else
      {
        $registry = Zend_Registry::getInstance();
      	$auth		= Zend_Auth::getInstance(); 
      	$adapter = new Smallunch_lib_Auth_Adapter($username, $password);
        $result = Zend_Auth::getInstance()->authenticate($adapter);
        
        if (!$result->isValid())
        {
          // Authentication failed; Set Message
          $this->view->message = implode('<br/>', $result->getMessages());
        }
          // User is valid
        else
        {
          // Store identity
          $auth->getStorage()->write($result->getIdentity());
          // Log user login
          $logger = Zend_Registry::get('logger');
          $logger->info('Successfull log in for ['.$auth->getIdentity()->first_name.', '.$auth->getIdentity()->last_name.'] From: '.$_SERVER['REMOTE_ADDR']);
          // If use is logged in, send them to the default module
          $defaultNamespace = new Zend_Session_Namespace('SMALLUNCH');
          #echo $defaultNamespace->requesturi;die();
          if (isset($defaultNamespace->requesturi)) {
            $uri = $defaultNamespace->requesturi;
            #echo $uri;die();
            unset($defaultNamespace->requesturi);
            $this->_redirect($uri, array('prependBase'=>'true'));
          }
          
          $this->_redirect('default/index/index');
        }
      }
    }
    
    $this->_helper->layout->setLayout('login');
    $this->view->title = "Log in";
  }
  
  public function logoutAction()
  {
    $logger = Zend_Registry::get('logger');
    $logger->info(Zend_Auth::getInstance()->getIdentity()->first_name.', '.Zend_Auth::getInstance()->getIdentity()->last_name.' Logged Off From: '.$_SERVER['REMOTE_ADDR']);
    Zend_Auth::getInstance()->clearIdentity();
    
    $this->_redirect('Login/index/index');
  }
}