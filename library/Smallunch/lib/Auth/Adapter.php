<?php
/**
 * Smallunch Auth adapter
 * Modified version done by
 * Jon from ZendCasts
 */
class Smallunch_lib_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
  /**
   * General Failure
   */
  const FAILURE                        =  "Failure";

  /**
   * Failure due to identity not being found.
   */
  const FAILURE_IDENTITY_NOT_FOUND     = "Identity Not Found";

  /**
   * Failure due to identity being ambiguous.
   */
  const FAILURE_IDENTITY_AMBIGUOUS     = "Identity Ambiguous";

  /**
   * Failure due to invalid credential being supplied.
   */
  const FAILURE_CREDENTIAL_INVALID     = "Credential Invalid";

  /**
   * Failure due to uncategorized reasons.
   */
  const FAILURE_UNCATEGORIZED          = "Failure Uncategorized";

  /**
   * Authentication success.
   */
  const SUCCESS                        = "Success";
  
  /**
   * User is not active
   */
  const FAILURE_NOT_ACTIVE            = "User is not active";
  
  /**
   *
   * @var Model_User
   */
  protected $user;

  /**
   *
   * @var string
   */
  protected $username;

  /**
   *
   * @var string
   */
  protected $password;

  public function __construct($username , $password)
  {
      $this->username = $username;
      $this->password = $password;
  }
  /**
   * Performs an authentication attempt
   *
   * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
   * @return Zend_Auth_Result
   */
  public function authenticate()
  {
    try
    {
      $user = Doctrine_Core::getTable('Users')->findOneByUsername($this->username);
      
      if (!$user)
      {
        throw new Exception(self::FAILURE_IDENTITY_NOT_FOUND);
      }
      
      if ($user->password != md5($this->password)) {
        throw new Exception(self::FAILURE_CREDENTIAL_INVALID);
      }
      elseif (strtolower($user->active) != 'true') {
        throw new Exception(self::FAILURE_NOT_ACTIVE);
      }
      // Remove password so we don't save it in storage (typically session)
      $user->password = '';
      // Setup Smallunch Layout
      $defaultNamespace = new Zend_Session_Namespace('SMALLUNCH');
      $defaultNamespace->layout = $user->UserRoles->layout;
      $defaultNamespace->role_id = $user->UserRoles->id;
      $this->user = $user;
        
    }
    catch (Exception $e)
    {
      if ($e->getMessage() == self::FAILURE_CREDENTIAL_INVALID) {
          return $this->result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, self::FAILURE_CREDENTIAL_INVALID);
      }
      elseif ($e->getMessage() == self::FAILURE_IDENTITY_NOT_FOUND) {
          return $this->result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, self::FAILURE_IDENTITY_NOT_FOUND);
      }
      elseif ($e->getMessage() == self::FAILURE_NOT_ACTIVE) {
          return $this->result(Zend_Auth_Result::FAILURE, self::FAILURE_NOT_ACTIVE);
      }
    }
    return $this->result(Zend_Auth_Result::SUCCESS);
  }

  /**
   * Factory for Zend_Auth_Result
   *
   *@param integer    The Result code, see Zend_Auth_Result
   *@param mixed      The Message, can be a string or array
   *@return Zend_Auth_Result
   */
  public function result($code, $messages = array())
  {
    if (!is_array($messages)) {
      $messages = array($messages);
    }

    return new Zend_Auth_Result(
        $code,
        $this->user,
        $messages
    );
  }
}