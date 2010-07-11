<?php
class Profile_IndexController extends Zend_Controller_Action
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
    $this->_forward("edit");
  }
  
  
  public function editAction()
  {
    $this->view->profile = $this->getProfileOrCreate();
    
    if ($this->getRequest()->isPost())
    {
      $this->updateProfileFromRequest();
      
      $this->setMessage('Your modifications have been saved');
      
      return $this->_redirect('/Profile/index/');
    }
  }
  
  
  protected function getProfileOrCreate()
  {
    try {
      $profile = Doctrine::getTable('Users')->find(Zend_Auth::getInstance()->getIdentity()->id);
    }
    catch (Exception $e) {
      $this->setError($e->getMessage());
    }
    
    return $profile;
  }
  
  protected function updateProfileFromRequest()
  {
    $profile = $this->_getParam('profile');
    $profile_rs = $this->getProfileOrCreate($this->_getParam('id'));
    if (trim($profile['password']) != trim($profile['password2'])) {
       $this->setError('Passwords do not match. Changes not saved.');
       return $this->_forward('index');
     }
     
    if (isset($profile['username'])) {
	      $profile_rs->username = $this->updateProfileUsername($profile['username']);
	    }
	    if (isset($profile['email'])) {
	      $profile_rs->email = $this->updateProfileEmail($profile['email']);
	    }
	    if (isset($profile['first_name'])) {
	      $profile_rs->first_name = $this->updateProfileFirst_name($profile['first_name']);
	    }
	    if (isset($profile['middle_name'])) {
	      $profile_rs->middle_name = $this->updateProfileMiddle_name($profile['middle_name']);
	    }
	    if (isset($profile['last_name'])) {
	      $profile_rs->last_name = $this->updateProfileLast_name($profile['last_name']);
	    }
	    if (isset($profile['address1'])) {
	      $profile_rs->address1 = $this->updateProfileAddress1($profile['address1']);
	    }
	    if (isset($profile['address2'])) {
	      $profile_rs->address2 = $this->updateProfileAddress2($profile['address2']);
	    }
	    if (isset($profile['city'])) {
	      $profile_rs->city = $this->updateProfileCity($profile['city']);
	    }
	    if (isset($profile['state'])) {
	      $profile_rs->state = $this->updateProfileState($profile['state']);
	    }
	    if (isset($profile['zip'])) {
	      $profile_rs->zip = $this->updateProfileZip($profile['zip']);
	    }
	    if (isset($profile['phone'])) {
	      $profile_rs->phone = $this->updateProfilePhone($profile['phone']);
	    }
	    if (isset($profile['cell'])) {
	      $profile_rs->cell = $this->updateProfileCell($profile['cell']);
	    }
	    if (isset($profile['fax'])) {
	      $profile_rs->fax = $this->updateProfileFax($profile['fax']);
	    }
	    if (isset($profile['organization'])) {
	      $profile_rs->organization = $this->updateProfileOrganization($profile['organization']);
	    }
	    if (isset($profile['contest_date'])) {
	      $profile_rs->contest_date = $this->updateProfileContest_date($profile['contest_date']);
	    }
	    if (isset($profile['contest_time'])) {
	      $profile_rs->contest_time = $this->updateProfileContest_time($profile['contest_time']);
	    }
	    if (isset($profile['contest_location'])) {
	      $profile_rs->contest_location = $this->updateProfileContest_location($profile['contest_location']);
	    }
	    if (isset($profile['password'])) {
	    	if (trim($profile['password']) == trim($profile['password2'])) {
	      $profile_rs->password = $this->updateProfilePassword($profile['password']);
	    	}
	    	else {
	    		$this->setError('Passwords do not match');
	    	}
	    }
	    if (isset($profile['user_role_id'])) {
	      $profile_rs->user_role_id = $this->updateProfileUser_role_id($profile['user_role_id']);
	    }
	    if (isset($profile['active'])) {
	      $profile_rs->active = $this->updateProfileActive($profile['active']);
	    }
	    if (isset($profile['user_notes'])) {
	      $profile_rs->user_notes = $this->updateProfileUser_notes($profile['user_notes']);
	    }
	    
    $profile_rs->save();
    
    
    $this->view->profile = $profile_rs;
    
  }
  
  
    protected function updateProfileUsername($username = '') {
   return  $username;
  }
    protected function updateProfileEmail($email = '') {
   return  $email;
  }
    protected function updateProfileFirst_name($first_name = '') {
   return  $first_name;
  }
    protected function updateProfileMiddle_name($middle_name = '') {
   return  $middle_name;
  }
    protected function updateProfileLast_name($last_name = '') {
   return  $last_name;
  }
    protected function updateProfileAddress1($address1 = '') {
   return  $address1;
  }
    protected function updateProfileAddress2($address2 = '') {
   return  $address2;
  }
    protected function updateProfileCity($city = '') {
   return  $city;
  }
    protected function updateProfileState($state = '') {
   return  $state;
  }
    protected function updateProfileZip($zip = '') {
   return  $zip;
  }
    protected function updateProfilePhone($phone = '') {
   return  $phone;
  }
    protected function updateProfileCell($cell = '') {
   return  $cell;
  }
    protected function updateProfileFax($fax = '') {
   return  $fax;
  }
    protected function updateProfileOrganization($organization = '') {
   return  $organization;
  }
    protected function updateProfileContest_date($contest_date = '') {
   return  $contest_date;
  }
    protected function updateProfileContest_time($contest_time = '') {
   return  $contest_time;
  }
    protected function updateProfileContest_location($contest_location = '') {
   return  $contest_location;
  }
    protected function updateProfilePassword($password = '') {
   return  md5(trim($password));
  }
    protected function updateProfileUser_role_id($user_role_id = '') {
   return  $user_role_id;
  }
    protected function updateProfileActive($active = '') {
   return  $active;
  }
    protected function updateProfileUser_notes($user_notes = '') {
   return  $user_notes;
  }
  
  public function deleteAction()
  {
    $profile = Doctrine::getTable('Users')->find($this->_getParam('id'));
    $profile->delete();
    $this->_forward("list");
  }
}
