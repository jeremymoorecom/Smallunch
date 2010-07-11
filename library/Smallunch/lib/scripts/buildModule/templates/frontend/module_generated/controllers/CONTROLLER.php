<?php
[[INFO]]
class Smallunch_generated_[[APP]]_modules_[[className]]_controllers_[[CONTROLLER_NAME]]Controller extends Zend_Controller_Action
{
  protected $logger;
  
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
        $baseUrl.'/[[moduleName]]/[[CONTROLLER_VIEW]]/list/page/{%page_number}?'.$append_url
    );
    
    $pager_layout->setTemplate('[<a href="{%url}">{%page}</a>]');
    $pager_layout->setSelectedTemplate('[{%page}]');
    
    $pager = $pager_layout->getPager();
    
    $this->view->[[moduleNameVar]]s = $pager->execute();
    
    $this->view->pager_layout = $pager_layout;
    $this->view->pager = $pager;
  }
  
  public function showAction($layout = '') {
    if ($layout != '') {
      $this->_helper->layout->setLayout($layout);
    }
    
    $[[moduleNameVar]]_rs = [[TABLE_WITH_RELATIONS]], $this->_getParam('id'))
               ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
    if ($[[moduleNameVar]]_rs)
    {
      $this->view->[[moduleNameVar]] = $[[moduleNameVar]]_rs;
    }
    else 
    {
      return $this->page_not_foundAction();
    } 
  }
  public function showAction_old($layout = '') {
    if ($layout != '') {
      $this->_helper->layout->setLayout($layout);
    }
    
    $[[moduleNameVar]]_rs = Doctrine_Query::create()
               ->from('[[TABLE]] [[TABLE_AS]]')
               ->where('[[TABLE_AS]].id=?', $this->_getParam('id'))
               ->fetchArray();
    if (count($[[moduleNameVar]]_rs) !=0)
    {
      $this->view->[[moduleNameVar]] = $[[moduleNameVar]]_rs[0];
    }
    else 
    {
      return $this->page_not_foundAction();
    } 
  }
  public function page_not_foundAction() {
    $this->render('404');
  }
}
