<?php
class Smallunch_lib_View_Helper_UserType extends Zend_View_Helper_FormSelect
{
  function userType($name, $value = null, $attribs = null, $options = null)
  {
  	$user_rs = Doctrine_Query::create()->from('UserRoles ur')->orderby('ur.name')->fetchArray();
  	foreach ($user_rs as $user) {
  		$options[$user['id']] = $user['name'];
  	}
  	return $this->formSelect($name, $value, $attribs, $options);
  }
}