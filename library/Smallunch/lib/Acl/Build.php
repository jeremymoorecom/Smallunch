<?php
class Smallunch_lib_Acl_Build extends Zend_Acl
{
  public function __construct(Zend_Auth $auth)
  {
    $administrator_role_id = '';
    // Build roles
    $role_rs = Doctrine_Query::create()->from('UserRoles ur')->orderBy('ur.inherit_id')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach  ($role_rs as $role)
    {
      if ($role['name'] == 'Administrator')
      {
        $administrator_role_id = $role['id'];
      }
      $role_array[$role['id']] = $role['id'];
      if($role['inherit_id'] !== null)
      {
        $this->addRole(new Zend_Acl_Role($role['id']), $role_array[$role['inherit_id']]);
      }
      else
      {
        $this->addRole(new Zend_Acl_Role($role['id']));
      }
    }
    // Add Resources for application enviroment
    $resources = Doctrine_Query::create()->from('AclResources ar')->where('ar.application=?', APP)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach ($resources as $resource)
    {
      $this->add(new Zend_Acl_Resource($resource['name']));
      $this->allow($role_array[$resource['role_id']], $resource['name']);
      // add Administrator user
      $this->allow($administrator_role_id);
    }
  }
}