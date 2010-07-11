<?php
[[INFO]]
class Smallunch_generated_[[APP]]_modules_[[className]]_controllers_[[CONTROLLER_NAME]]Controller extends Zend_Controller_Action
{
  protected $logger;
  
  public function preDispatch()
  {
    // Authenticate
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
    $this->_forward("list");
  }
  
  protected function listQuery($filter)
  {
    $doc = Doctrine_Query::create()->from( '[[TABLE]] [[TABLE_AS]]' );
    if (is_array($filter)) {
      [[LIST_QUERY_FILTERS]]
    }
    
    return $doc;
  }
  
  public function listAction()
  {
  	$sessionNamespace = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRequest()->getModuleName());
    [[LIST_DEFAULTS]]
    
    if (!$currentPage = $this->_getParam('page'))
    {
      $currentPage =1;
    }
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    
    // add filter to pager
    $append_url = array();
    if ($filter) {
      foreach ($filter as $k => $v) {
        $append_url[] = 'filter['.$k.']='.$v;
      }
    }
    $append_url = implode('&', $append_url);
    
    $doc = $this->listQuery($filter);
    
    // Creating pager layout
    $pager_layout = new Doctrine_Pager_Layout(
        new Doctrine_Pager(
          $doc,
          $currentPage,
          $resultsPerPage
        ),
        new Doctrine_Pager_Range_Sliding(array(
            'chunk' => 5
        )),
        $baseUrl.'/[[moduleName]]/[[CONTROLLER_NAME]]/list/page/{%page_number}?'.$append_url
    );
    
    $pager_layout->setTemplate('[<a href="{%url}">{%page}</a>]');
    $pager_layout->setSelectedTemplate('[{%page}]');
    
    $pager = $pager_layout->getPager();
    
    $this->view->[[moduleNameVar]]s = $pager->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    
    $this->view->pager_layout = $pager_layout;
    $this->view->pager = $pager;
  }
  
  public function editAction()
  {
    $this->view->[[moduleNameVar]] = $this->get[[moduleName]]OrCreate($this->_getParam('id'));
    
    if ($this->getRequest()->isPost())
    {
      $this->update[[moduleName]]FromRequest();
      
      $this->setMessage('Your modifications have been saved');
      
      if ($this->_getParam('save_and_add'))
      {
        return $this->_redirect('/[[moduleName]]/[[CONTROLLER_NAME]]/edit/');
      }
      else if ($this->_getParam('save_and_list'))
      {
        return $this->_redirect('/[[moduleName]]/[[CONTROLLER_NAME]]/list/');
      }
      else
      {
        return $this->_redirect('/[[moduleName]]/[[CONTROLLER_NAME]]/edit/id/'.$this->view->[[moduleNameVar]]->id);
      }
    }
  }

  public function prevAction()
  {
    $sessionNamespace = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRequest()->getModuleName());
    $filter = $sessionNamespace->filter;
    $doc = $this->listQuery($filter);
    $doc->addWhere('[[TABLE_AS]].id < ?', $this->_getParam('id'))->orderBy('[[TABLE_AS]].id DESC');
    $prev = $doc->fetchOne(array(), DOCTRINE_CORE::HYDRATE_ARRAY);
    if ($prev) {
      $this->getRequest()->setParam('id', $prev['id']);
    }
    
    $this->_forward('edit');
  }
  
  public function nextAction()
  {
    $sessionNamespace = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRequest()->getModuleName());
    $filter = $sessionNamespace->filter;
    $doc = $this->listQuery($filter);
    $doc->addWhere('[[TABLE_AS]].id > ?', $this->_getParam('id'));
    $next = $doc->fetchOne(array(), DOCTRINE_CORE::HYDRATE_ARRAY);
    if ($next) {
      $this->getRequest()->setParam('id', $next['id']);
    }
    
    $this->_forward('edit');
  }
  
  protected function get[[moduleName]]OrCreate($id = '')
  {
    if ($id == '' || $id === null)
    {
      $[[moduleNameVar]] = new [[TABLE]]();
    }
    else
    {
      try {
        if ($this->getRequest()->isPost())
        {
          $[[moduleNameVar]] = Doctrine_Core::getTable('[[TABLE]]')->find($id);
        }
        else
        {
          $[[moduleNameVar]] = Doctrine_Query::create()->from('[[TABLE]] [[TABLE_AS]]')->where('[[TABLE_AS]].id =?',$id)->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
        }
      }
      catch (Exception $e) {
        $this->setError($e->getMessage());
      }
    }
    
    return $[[moduleNameVar]];
  }
  
  protected function update[[moduleName]]FromRequest()
  {
    $[[moduleNameVar]] = $this->_getParam('[[moduleNameVar]]');
    $[[moduleNameVar]]_rs = $this->get[[moduleName]]OrCreate($this->_getParam('id'));
    
    [[saveFields]]
    $[[moduleNameVar]]_rs->save();
    
    
    $this->view->[[moduleNameVar]] = $[[moduleNameVar]]_rs;
    
  }
  
  [[update_functions]]
  
  public function deleteAction()
  {
    $[[moduleNameVar]] = Doctrine_Core::getTable('[[TABLE]]')->find($this->_getParam('id'));
    $[[moduleNameVar]]->delete();
    $this->_forward("list");
  }
}
