<?php
class Smallunch_lib_View_Helper_UserType extends Zend_View_Helper_FormSelect
{
  /**
   * Returns selectbox generated from UserRoles
   * 
   * @param $name Name for form element
   * @param $value  Selected Option value
   * @param $attribs Attributes added to the 'select' tag.
   * @param $options 
   */
  function userType($name = '', $value = null, $attribs = null, $options = array())
  {
    $roles = array();
  	$user_rs = Doctrine_Query::create()->from('UserRoles ur')->orderby('ur.name')->fetchArray();
  	
  	foreach ($user_rs as $user) {
  		$roles[$user['id']] = $user['name'];
  	}
  	return $this->formSelect($name, $value, $attribs, array_merge($options, $roles));
  }
}