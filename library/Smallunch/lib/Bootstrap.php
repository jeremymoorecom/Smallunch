<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public $config;
  protected function _initCache()
    {
    	if (APPLICATION_ENV != 'development' && APP != 'backend') {
        $config = new Zend_Config_Ini(SMCONFIG_DIRECTORY.DIRECTORY_SEPARATOR.'cache.ini', 'general');
        if ($config->enable == true)
        {
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
	 
	        $cache->start();
        }
    	}
    }
	public function _initConfig()
	{
		$this->config = new Zend_Config_Ini(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ini', 'general');
		// setup globals from config
		define('HTML_DIR', $this->config->dir->public_html);
		define('IMAGE_DIR', $this->config->dir->images);
		define('IMAGE_UPLOAD_DIR', $this->config->dir->upload_images);
		define('ADMIN_DIR', $this->config->dir->admin);
	}

	public function _initSession()
	{
		$config = new Zend_Config_Ini(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR .  'config'.DIRECTORY_SEPARATOR.'session.ini', 'development');
		
		// Flash has problems with cookies so we pass the PHPSESSID variable via get
	  if (substr_count($_SERVER['REQUEST_URI'], 'sessionid/') != 0) {
			$start = stripos($_SERVER['REQUEST_URI'], 'sessionid/') + 10;
			$end = strpos($_SERVER['REQUEST_URI'], '/', $start);

			if ($end > $start) {
			 $sid = substr($_SERVER['REQUEST_URI'], $start, $end);
			}
			else {
				$sid = substr($_SERVER['REQUEST_URI'], $start);
			}
			
			$prefix = '';
			if (!empty($_SERVER["HTTP_COOKIE"])) {
			$prefix = '; ';
			}
			
			$_SERVER["HTTP_COOKIE"] = $prefix . $config->name . '=' . $sid;
			$_COOKIE[$config->name] = $sid;
			
			Zend_Session::setId($sid);
		}
		
		Zend_Session::setOptions($config->toArray());
	}
	
	public function _initLogger()
	{
		if (APPLICATION_ENV == 'development') {
      $writer = new Zend_Log_Writer_Firebug();
    }
    // Logger
    elseif ($this->config->log->enabled == false) {
      $writer = new Zend_Log_Writer_Null();
    }
    else {
      $writer = new Zend_Log_Writer_Stream(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.$this->config->log->filepath.DIRECTORY_SEPARATOR.$this->config->log->filename);
    }
    
    $logger = new Zend_Log($writer);
    Zend_Registry::set('logger',$logger);
	}
	
	public function _initView()
	{
		// Initialize view
    $view = new Zend_View();
    // Setup Helper Paths
    $view->setHelperPath(SM_LIB.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'Helper', 'Smallunch_lib_View_Helper');
    // Add Global Helper path (typically application/helpers
    $view->addHelperPath(GLOBAL_HELPER_DIR . DIRECTORY_SEPARATOR.'View');
    // add per application helper path (typically application/(backend, frontend)/helpers
    $view->addHelperPath(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'Helper'.DIRECTORY_SEPARATOR.'View');
    // Setup layout
    if (isset($this->config->layout->doctype)) {
      $view->doctype($this->config->layout->doctype);
    }
    if (isset($this->config->default->title)) {
      $view->headTitle($this->config->default->title);
    }
    if (isset($this->config->default->meta_keywords)) {
    	$view->headMeta($this->config->default->meta_keywords, 'keywords');
    }
    if (isset($this->config->default->meta_description)) {
    	$view->headMeta($this->config->default->meta_description, 'description');
    }
	  if(isset($this->config->layout->charset)) {
      $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset='.$this->config->layout->charset);
    }
    if (isset($this->config->layout->content_language)) {
    	$view->headMeta()->appendHttpEquiv('Content-Language', $this->config->layout->content_language);
    }
    // Add it to the ViewRenderer
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setView($view);

    // Return it, so that it can be stored by the bootstrap
    return $view;
	}
	
  protected function _initDoctrine()
  {
  	$database = new Zend_Config_Ini(SMCONFIG_DIRECTORY.DIRECTORY_SEPARATOR.'database.ini', 'database');
  	$doctrineConfig = $this->getOption('doctrine');
    $this->getApplication()->getAutoloader()
                           ->pushAutoloader(array('Doctrine', 'autoload'));
    spl_autoload_register(array('Doctrine', 'modelsAutoload'));
    $manager = Doctrine_Manager::getInstance();
    $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
    $manager->setAttribute(
        Doctrine_Core::ATTR_MODEL_LOADING,
        Doctrine_Core::MODEL_LOADING_CONSERVATIVE
    );
    $manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
    $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL); // added for validation

    // Add models and generated base classes to Doctrine autoloader
    Doctrine_Core::loadModels($doctrineConfig['models_path']);
    // smallunch setup doctrine
    $manager->openConnection($database->db->doctrine_adapter."://".$database->db->params->username.":".$database->db->params->password."@".$database->db->params->host.($database->db->params->port != '' ? ":".$database->db->params->port : "")."/".$database->db->params->dbname.($database->db->params->socket != '' ? ";unix_socket=".$database->db->params->socket : ""));    

    $profiler = new Doctrine_Connection_Profiler();
    // Add to registry
    Zend_Registry::set('doctrine_conn',$profiler);
    
    return $manager;
  }
    
  public function _initACL()
  {
  	if (isset($this->config->acl) && $this->config->acl->enabled == true) {
			// Create auth object
			$auth = Zend_Auth::getInstance();
			$auth->setStorage(new Zend_Auth_Storage_Session('SmallunchAuth'));
			// Create acl object
			Zend_Registry::set('acl', new Smallunch_lib_Acl_Build($auth));
  	}
  }
  
  public function _initSmallunchFrontControllerPlugins()
  {
    $frontController = Zend_Controller_Front::getInstance();
    $frontController->registerPlugin(new Smallunch_lib_plugins_Layout());
    $frontController->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
       'module'     => 'error',
       'controller' => 'error',
       'action'     => 'error'
    )));
  }
  
protected  function _initActionHelper()
  {
    Zend_Controller_Action_HelperBroker::addPath(SM_LIB.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.'Action'.DIRECTORY_SEPARATOR.'Helper', 'Smallunch_lib_Controller_Action_Helper');
    Zend_Controller_Action_HelperBroker::addPath(GLOBAL_HELPER_DIR . DIRECTORY_SEPARATOR.'Controller', 'Application_Helper_Controller');
    Zend_Controller_Action_HelperBroker::addPath(APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR.'Controller', 'Application_'.APP.'_Helper_Controller');
  }
	
	protected function _initZFDebug()
	{
		if (APPLICATION_ENV == 'development') {
	    $autoloader = Zend_Loader_Autoloader::getInstance();
	    $autoloader->registerNamespace('Smallunch_lib_bundles_ZFDebug');
	    $doctrine = $this->getResource('doctrine_conn');
	    $options = array(
	        'plugins' => array('Variables', 
	                           'File' => array('base_path' => ROOT_DIRECTORY),
	                           'Memory', 
	                           'Time', 
	                           'Registry', 
	                           'Smallunch_lib_bundles_ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine',
	                           'Exception')
	    );
	    
	
	    # Setup the cache plugin
	    if ($this->hasPluginResource('cache')) {
	        $this->bootstrap('cache');
	        $cache = $this-getPluginResource('cache')->getDbAdapter();
	        $options['plugins']['Cache']['backend'] = $cache->getBackend();
	    }
	    
	    $this->bootstrap('frontController');
	    $frontController = $this->getResource('frontController');
	    $frontController->registerPlugin(new Smallunch_lib_bundles_ZFDebug_Controller_Plugin_Debug($options));
		}
	}
	
	/**
	 * Install user plugins
	 */
	protected function _initPlugins()
	{
	  $plugins = new Zend_Config_Ini(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'plugins.ini', APPLICATION_ENV);
	  $plugins = $plugins->toArray();
	  
	  if (!empty($plugins))
	  {
	    $this->bootstrap('frontController');
      $frontController = $this->getResource('frontController');
  	  foreach ($plugins as $plugin => $options)
  	  {
  	    if (array_key_exists('name', $options) && $options['name'] != '')
  	    {
  	      $frontController->registerPlugin(new $options['name']($options));
  	    }
  	  }
	  }
	}
	
  protected function _initCli()
  {
    
  }
}

