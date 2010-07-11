<?php
class Cache_IndexController extends Zend_Controller_Action
{
  protected $logger;
  public function preDispatch()
  {
    // Set ACL Here
    require_once 'Smallunch/lib/Auth/preDispatch.php';
  }
  public function init()
  {
    $this->logger = Zend_Registry::get("logger");
  }
  
	public function __call($method, $args)
	{
		if (APPLICATION_ENV == 'development') {
	    throw new Zend_Controller_Exception('Invalid method called');
		}
	}
	
  public function indexAction()
  { 
  }

  public function page_not_foundAction() {
    $this->render('404');
  }
  
  public function clearAction()
  {
  	$json = array('message'=>'');
      $config = new Zend_Config_Ini(SMCONFIG_DIRECTORY.DIRECTORY_SEPARATOR.'cache.ini', 'general');
        
          $frontendOptions = array(
              'lifetime' => $config->lifetime,
              'automatic_serialization' => $config->automatic_serialization,
              'debug_header' => $config->debug_header,
              'default_options' => array(
                'cache_with_get_variables' => $config->cache_with_get_variables,
                'cache_with_post_variables' => $config->cache_with_post_variables,
                'cache_with_session_variables' => $config->cache_with_session_variables,
                'cache_with_files_variables' => $config->cache_with_files_variables,
                'cache_with_cookie_variables' => $config->cache_with_cookie_variables,
                'make_id_with_get_variables' => $config->make_id_with_get_variables,
                'make_id_with_post_variables' => $config->make_id_with_post_variables,
                'make_id_with_session_variables' => $config->make_id_with_session_variables,
                'make_id_with_files_variables' => $config->make_id_with_files_variables,
                'make_id_with_cookie_variables' => $config->make_id_with_cookie_variables,
                ),
          'regexps' => array(
                  // cache the whole IndexController
                  '^/$' => array('cache' => true),
                  // cache the whole IndexController
                  '^/index/' => array('cache' => true),
                   )
          );
  
          $backendOptions = array(
                  'cache_dir' =>ROOT_DIRECTORY.'/cache/'
          );
  
          // getting a Zend_Cache_Frontend_Page object
          $cache = Zend_Cache::factory('Page',
                               'File',
                               $frontendOptions,
                               $backendOptions);
   
          
    // clean all records
    if ($cache->clean(Zend_Cache::CLEANING_MODE_ALL)) {
    	$json['message'] = "Cache Cleared";
    }
    
    $this->_helper->json($json);
  }
}
?>
