<?php
class Users_IndexController extends Zend_Controller_Action
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
    $this->_forward("list");
  }
  
  protected function listQuery($filter)
  {
    $doc = Doctrine_Query::create()->from( 'Users l' );
    if (is_array($filter)) {
      (key_exists('id', $filter) && trim($filter['id'])!='' ? $doc->addWhere('l.id LIKE ?', $filter['id']) : '');
  		(key_exists('username', $filter) && trim($filter['username'])!='' ? $doc->addWhere('l.username LIKE ?', $filter['username']) : '');
  		(key_exists('email', $filter) && trim($filter['email'])!='' ? $doc->addWhere('l.email LIKE ?', $filter['email']) : '');
  		(key_exists('first_name', $filter) && trim($filter['first_name'])!='' ? $doc->addWhere('l.first_name LIKE ?', $filter['first_name']) : '');
  		(key_exists('middle_name', $filter) && trim($filter['middle_name'])!='' ? $doc->addWhere('l.middle_name LIKE ?', $filter['middle_name']) : '');
  		(key_exists('last_name', $filter) && trim($filter['last_name'])!='' ? $doc->addWhere('l.last_name LIKE ?', $filter['last_name']) : '');
  		(key_exists('address1', $filter) && trim($filter['address1'])!='' ? $doc->addWhere('l.address1 LIKE ?', $filter['address1']) : '');
  		(key_exists('address2', $filter) && trim($filter['address2'])!='' ? $doc->addWhere('l.address2 LIKE ?', $filter['address2']) : '');
  		(key_exists('city', $filter) && trim($filter['city'])!='' ? $doc->addWhere('l.city LIKE ?', $filter['city']) : '');
  		(key_exists('state', $filter) && trim($filter['state'])!='' ? $doc->addWhere('l.state LIKE ?', $filter['state']) : '');
  		(key_exists('zip', $filter) && trim($filter['zip'])!='' ? $doc->addWhere('l.zip LIKE ?', $filter['zip']) : '');
  		(key_exists('phone', $filter) && trim($filter['phone'])!='' ? $doc->addWhere('l.phone LIKE ?', $filter['phone']) : '');
  		(key_exists('cell', $filter) && trim($filter['cell'])!='' ? $doc->addWhere('l.cell LIKE ?', $filter['cell']) : '');
  		(key_exists('fax', $filter) && trim($filter['fax'])!='' ? $doc->addWhere('l.fax LIKE ?', $filter['fax']) : '');
  		(key_exists('organization', $filter) && trim($filter['organization'])!='' ? $doc->addWhere('l.organization LIKE ?', $filter['organization']) : '');
  		(key_exists('password', $filter) && trim($filter['password'])!='' ? $doc->addWhere('l.password LIKE ?', $filter['password']) : '');
  		(key_exists('user_role_id', $filter) && trim($filter['user_role_id'])!='' ? $doc->addWhere('l.user_role_id LIKE ?', $filter['user_role_id']) : '');
  		(key_exists('active', $filter) && trim($filter['active'])!='' ? $doc->addWhere('l.active LIKE ?', $filter['active']) : '');
  		(key_exists('user_notes', $filter) && trim($filter['user_notes'])!='' ? $doc->addWhere('l.user_notes LIKE ?', $filter['user_notes']) : '');
  		(key_exists('created_at', $filter) && trim($filter['created_at'])!='' ? $doc->addWhere('l.created_at LIKE ?', $filter['created_at']) : '');
  		(key_exists('updated_at', $filter) && trim($filter['updated_at'])!='' ? $doc->addWhere('l.updated_at LIKE ?', $filter['updated_at']) : '');
  		
    }
    
    return $doc;
  }
  
  public function listAction()
  {
    
  	// Set Defaults
  	$heading = 'Users';
  	$display = array('id','username','email','first_name','middle_name','last_name','address1','address2','city','state','zip','phone','cell','fax','organization','password','user_role_id','active','user_notes','created_at','updated_at');
  	$filters = array('id','username','email','first_name','middle_name','last_name','address1','address2','city','state','zip','phone','cell','fax','organization','password','user_role_id','active','user_notes','created_at','updated_at');
  	$labels = array('id','username','email','first_name','middle_name','last_name','address1','address2','city','state','zip','phone','cell','fax','organization','password','user_role_id','active','user_notes','created_at','updated_at');
  	$resultsPerPage = 20;
  	
  	$configFile = APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.Zend_Controller_Front::getInstance()->getRequest()->getModuleName().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.yml';
  	if (!file_exists($configFile)) {
  		$configFile = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.yml');
  	}
    
    if (file_exists($configFile))
    {
      $config = sfYaml::load($configFile);
      $this->view->ymlConfig = $config['list'];
      (key_exists('title', $config['list']) ? $heading = $config['list']['title'] : '');
      (key_exists('display', $config['list']) ? $display = $config['list']['display'] : '');
      (key_exists('filters', $config['list']) ? $filters = $config['list']['filters'] : '');
      (key_exists('perpage', $config['list']) ? $resultsPerPage = $config['list']['perpage'] : '');
      $labels = $config['labels'];
    }
    
    $this->view->labels = $labels;
    $this->view->display = $display;
    $this->view->heading = $heading;
    $filter = $this->_getParam('filter');
    
    foreach ($filters as $y)
    {
      $filters2[$y] = (is_array($filter) && key_exists($y, $filter) ? $filter[$y] : '');
    }
    
    $this->view->filter = $filters2;
    
    
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
        $baseUrl.'/Users/index/list/page/{%page_number}?'.$append_url
    );
    
    $pager_layout->setTemplate('[<a href="{%url}">{%page}</a>]');
    $pager_layout->setSelectedTemplate('[{%page}]');
    
    $pager = $pager_layout->getPager();
    
    $this->view->list = $pager->execute();
    
    $this->view->pager_layout = $pager_layout;
    $this->view->pager = $pager;
  }
  
  public function editAction()
  {
    $this->view->users = $this->getUsersOrCreate($this->_getParam('id'));
    
    if ($this->getRequest()->isPost())
    {
      $this->updateUsersFromRequest();
      
      $this->setMessage('Your modifications have been saved');
      
      if ($this->_getParam('save_and_add'))
      {
        return $this->_redirect('/Users/index/edit/');
      }
      else if ($this->_getParam('save_and_list'))
      {
        return $this->_redirect('/Users/index/list/');
      }
      else
      {
        return $this->_redirect('/Users/index/edit/id/'.$this->view->users->id);
      }
    }
  }
  
  
  protected function getUsersOrCreate($id = 'id')
  {
    if ($id == '' || $id === null)
    {
      $users = new Users();
    }
    else
    {
      try {
        $users = Doctrine::getTable('Users')->find($this->_getParam('id'));
      }
      catch (Exception $e) {
        $this->setError($e->getMessage());
      }
    }
    
    return $users;
  }
  
  
  
    protected function updateUsersUsername($username = '') {
   return  $username;
  }
    protected function updateUsersEmail($email = '') {
   return  $email;
  }
    protected function updateUsersFirst_name($first_name = '') {
   return  $first_name;
  }
    protected function updateUsersMiddle_name($middle_name = '') {
   return  $middle_name;
  }
    protected function updateUsersLast_name($last_name = '') {
   return  $last_name;
  }
    protected function updateUsersAddress1($address1 = '') {
   return  $address1;
  }
    protected function updateUsersAddress2($address2 = '') {
   return  $address2;
  }
    protected function updateUsersCity($city = '') {
   return  $city;
  }
    protected function updateUsersState($state = '') {
   return  $state;
  }
    protected function updateUsersZip($zip = '') {
   return  $zip;
  }
    protected function updateUsersPhone($phone = '') {
   return  $phone;
  }
    protected function updateUsersCell($cell = '') {
   return  $cell;
  }
    protected function updateUsersFax($fax = '') {
   return  $fax;
  }
    protected function updateUsersOrganization($organization = '') {
   return  $organization;
  }
    protected function updateUsersPassword($password = '') {
   return  md5(trim($password));
  }
    protected function updateUsersUser_role_id($user_role_id = '') {
   return  $user_role_id;
  }
    protected function updateUsersActive($active = '') {
   return  $active;
  }
    protected function updateUsersUser_notes($user_notes = '') {
   return  $user_notes;
  }
  
  public function deleteAction()
  {
    $users = Doctrine::getTable('Users')->find($this->_getParam('id'));
    $users->delete();
    $this->_forward("list");
  }
public function ajaxusernameAction()
  {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $res_rs = Doctrine_Query::create()->from('Users u')->where('u.username=?', $this->_getParam('name'));
    if ($this->_getParam('id')!='') {
      $res_rs->addWhere('u.id !=?', $this->_getParam('id'));
    }
    if ($res_rs->count() == 0) {
      echo 'OK';
    }
    else 
    {
      echo 'FAIL';
    }
  }

  protected function updateUsersFromRequest()
  {
    $users = $this->_getParam('users');
    // validation for user data
    $error = false;
    if (trim($users['username']) == '') {
      $this->setError('Username is not valid');
      $error = true;
    }
    else 
    {
      $res_rs = Doctrine_Query::create()->from('Users u')->where('u.username=?', $users['username']);
      if ($this->_getParam('id')!='') {
        $res_rs->addWhere('u.id !=?', $this->_getParam('id'));
      }
      if ($res_rs->count() != 0) 
      {
        $this->setError('Username already in use.');
        $error = true;
      }
    }
    if ($this->_getParam('id') == '') {
      if (trim($users['password']) == '') {
        $this->setError('Password not specified');
        $error = true;
      }
      if (trim($users['password']) != trim($users['password2'])) {
        $this->setError('Passwords do not match');
        $error = true;
      }
    }
    if ($error == true) {
      return $this->_redirect('/Users/index/edit?create=New');
    }
    // end validation
    $users_rs = $this->getUsersOrCreate($this->_getParam('id'));
    
    if (isset($users['username'])) {
        $users_rs->username = $this->updateUsersUsername($users['username']);
      }
      if (isset($users['email'])) {
        $users_rs->email = $this->updateUsersEmail($users['email']);
      }
      if (isset($users['first_name'])) {
        $users_rs->first_name = $this->updateUsersFirst_name($users['first_name']);
      }
      if (isset($users['middle_name'])) {
        $users_rs->middle_name = $this->updateUsersMiddle_name($users['middle_name']);
      }
      if (isset($users['last_name'])) {
        $users_rs->last_name = $this->updateUsersLast_name($users['last_name']);
      }
      if (isset($users['address1'])) {
        $users_rs->address1 = $this->updateUsersAddress1($users['address1']);
      }
      if (isset($users['address2'])) {
        $users_rs->address2 = $this->updateUsersAddress2($users['address2']);
      }
      if (isset($users['city'])) {
        $users_rs->city = $this->updateUsersCity($users['city']);
      }
      if (isset($users['state'])) {
        $users_rs->state = $this->updateUsersState($users['state']);
      }
      if (isset($users['zip'])) {
        $users_rs->zip = $this->updateUsersZip($users['zip']);
      }
      if (isset($users['phone'])) {
        $users_rs->phone = $this->updateUsersPhone($users['phone']);
      }
      if (isset($users['cell'])) {
        $users_rs->cell = $this->updateUsersCell($users['cell']);
      }
      if (isset($users['fax'])) {
        $users_rs->fax = $this->updateUsersFax($users['fax']);
      }
      if (isset($users['organization'])) {
        $users_rs->organization = $this->updateUsersOrganization($users['organization']);
      }
      if (isset($users['password'])) {
        $users_rs->password = $this->updateUsersPassword($users['password']);
      }
      if (isset($users['user_role_id'])) {
        $users_rs->user_role_id = $this->updateUsersUser_role_id($users['user_role_id']);
      }
      if (isset($users['active'])) {
        $users_rs->active = $this->updateUsersActive($users['active']);
      }
      if (isset($users['user_notes'])) {
        $users_rs->user_notes = $this->updateUsersUser_notes($users['user_notes']);
      }
      
    $users_rs->save();
    
    
    $this->view->users = $users_rs;
    
  }
}
