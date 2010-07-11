<?php
$defaultNamespace = new Zend_Session_Namespace('SMALLUNCH');
// Our Default preDispatch function
if (!Zend_Auth::getInstance()->hasIdentity())
{
  $defaultNamespace->requesturi = $this->getRequest()->getPathInfo();
  Zend_Auth::getInstance()->clearIdentity();
  $this->_redirect('/Login/index/index');
}
// Make sure user has access to this module
elseif (!Zend_Registry::get('acl')->isAllowed(Zend_Auth::getInstance()->getIdentity()->user_role_id, 
    Zend_Controller_Front::getInstance()->getRequest()->getModuleName()))
{
  $this->_redirect('/default/index/index');
}

// Make sure layout is correct for the user role
if (!isset($defaultNamespace->layout) || Zend_Auth::getInstance()->getIdentity()->user_role_id != $defaultNamespace->role_id) {
  $res_rs = Doctrine_Query::create()->from('UserRoles ur')->where('ur.id=?', Zend_Auth::getInstance()->getIdentity()->user_role_id)->limit(1)->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
  $defaultNamespace->layout = $res_rs['layout'];
  $defaultNamespace->role_id = Zend_Auth::getInstance()->getIdentity()->user_role_id;
}
$this->_helper->layout->setLayout($defaultNamespace->layout);
