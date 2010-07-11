<?php
require_once(realpath(dirname(__FILE__)).'/../../includes/bootstrap_doctrine.php'); 
class handler
{
  public $app;                  // frontend / backend
  public $moduleName;           // name of module to create
  public $tableName;            // name of table to use (Doctrine class name)
  public $table_as;             // used for Doctrine queries ->from('$tableName $table_as') would be ->from('table t')
	public $moduleNameGenerated;
  public $saveFields;
  public $edit_form_fields; // holds built form elements for _edit_form.phtml
  public $update_functions; // holds built function for updating table fields
  public $list_defaults;
  public $list_query_filters; // holds query for filters in controller class
  public $updateFromRequestFields;  // used in update[TableName]FromRequest. checks if field passed by user.
  public $template;         // template directory to use for building module
  public $start_write_path; // Tells system where to write module files (generated, application backend/frontend
  public $list_th;        // th for list view on frontend
  public $list_td;      // td for list view on frontend
  public $controller_name; // name used for controller, default to 'Index'
  public $info;
  public $overwrite;    // tells buildModule to rewrite file if exists
  public $is_extends;   // tells buildModule if this extends generated module files.
  public $extends_string; // code to put in extends files
  public $filters;    // input fields for frontend filters
  public $frontendList; // used by frontend list view, links to showAction
  public $frontendShow; // used to display fields in frontend showAction
  public $generated_dir; // path to smallunch generated directory
  public $table_with_relations; // query with relations, used to replace fetching object
  public $tables = array(); // used to hold what tables have already been included in table_with_relations query
  public $table_aliases = array(); // used to hold aliases used in query with relations, so we don't duplicate
  
  /**
   * Bootstrap and set variables
   * @param array $argv
   */
	public function __construct($argv)
	{ 
	  //$this->generated_dir = GENERATED;
	  $this->overwrite = 'false';
	  $this->is_extends = 'false';
	  
	  if (!array_key_exists('0', $argv) || ($argv[0] != 'backend' && $argv[0] != 'frontend')) {
	    unset($argv);
	    while (!isset($argv))
	    {
	      fwrite(STDOUT, "Build Module For? (backend, frontend) or type 'exit': ");
	      $user = trim(fgets(STDIN));
	      
	      if ($user == 'exit') {
	        exit(0);
	      }
	      elseif ($user != '' && ($user == 'backend' || $user == 'frontend')) {
	        $argv[0] = $user;
	        $this->generated_dir .= $argv[0];
	      }
	    }
	  }
	
    if ($argv[0] == 'help') {
      $this->help();
      exit(0);
    }
    // set location for generated modules (based on application frontend or backend
    $this->generated_dir = GENERATED_MODULES.DIRECTORY_SEPARATOR.$argv[0];
    // Module Name
	  if (!array_key_exists('1', $argv)) {
	    while (!array_key_exists('1', $argv))
      {
        fwrite(STDOUT, "Enter a ModuleName: ");
        $user = trim(fgets(STDIN));
        
        if ($user == 'exit') {
          exit(0);
        }
        elseif ($user != '') {
          $argv[1] = $this->camelCase($user);
          $this->moduleName = $this->camelCase($argv[1]);
        }
      }
      // Controller Name
      fwrite(STDOUT, "Enter a Controller Name (default Index): ");
      $user = trim(fgets(STDIN));
      
      if ($user == 'exit') {
        exit(0);
      }
      elseif ($user != '') {
        $this->controller_name = ucfirst(strtolower($this->camelCase($user)));
      }
      else {
        $this->controller_name = 'Index';
      }
	    // Table Name
	    $tb_name = '';
	    while ($tb_name == '')
	    {
        fwrite(STDOUT, "Table for CRUD (default none): ");
        $user = trim(fgets(STDIN));
        
        if ($user == 'exit') {
          exit(0);
        }
        elseif ($user != '') {
          if (!class_exists($user)) {
            fwrite(STDOUT, "Doctrine Class ".$user." does not exist\n");
          }
          else {
            $tb_name = $user;
            $argv[2] = $user;
          }
        }
        else {
          $tb_name = 'none';
        }
	    }
    }
	  
	  if (!file_exists(PROJECT_LOCATION.'/library/')) {
	    $this->kill("cannot run from ",PROJECT_LOCATION);
	  }
	  
	  // In Order to specify controller, we'll use an underscore
	  if ($this->moduleName == '') {
  	  if (substr_count($argv[1], '_') > 0) {
  	    $temp = explode('_', $argv[1]);
  	    $this->controller_name = ucfirst(strtolower(array_pop($temp)));
  	    if (is_array($temp)) {
  	     $this->moduleName = ($this->camelCase(implode(' ', $temp)));
  	    }
  	    else {
  	      $this->moduleName = ucfirst($this->camelCase($temp));
  	    }
  	  }
  	  else {
  	    $this->moduleName = $this->camelCase($argv[1]);
  	    $this->controller_name = 'Index';
  	  }
	  }
	  $this->app = $argv[0];
    
    $this->moduleNameGenerated = $this->moduleName;
    $this->moduleNameVar = strtolower($this->moduleName);
    
	  if (array_key_exists(2, $argv)) {
      if (!class_exists($argv[2])) {
        $this->error($argv['2']." class does not exist.");
        $this->help();
      }
      $this->tableName = $argv[2];
      $tmp = explode(' ', trim(preg_replace('/([A-Z])/', ' $1', $this->tableName)));
      $as = '';
      
      foreach ($tmp as $t) {
        $as .= $t[0];
      }
      $this->table_as = strtolower($as);
      unset($tmp, $as);
    }
    $this->info();
		$this->process();
	}
	
	/**
	 * Direct the build process
	 */
	private function process()
	{
		if ($this->app == 'backend' && isset($this->tableName))
		{
		  $this->overwrite = 'true';
      $this->build_list_functions();
      $this->createUpdateFields();
      $this->createEditFormFields();
      
      if (!file_exists($this->generated_dir.DIRECTORY_SEPARATOR.'modules')) { mkdir($this->generated_dir.DIRECTORY_SEPARATOR.'modules'); }
      
      $this->template = "backend/module_generated";
      $this->start_write_path = $this->generated_dir.DIRECTORY_SEPARATOR.'modules';
      $writepath = $this->start_write_path.DIRECTORY_SEPARATOR.$this->moduleNameGenerated;
      if (!file_exists($writepath)) { mkdir($writepath); }
      // build generated module
      $this->buildModule();
      
      // set overwrite to false and build application module
      $this->overwrite = 'false';
      $this->is_extends = 'true';
      /*$this->extends_string = "<?php require_once ($this->generated_dir_MODULES.DIRECTORY_SEPARATOR.'[[APP]]'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'[[className]]'.'[[FILE_PATH]]'.DIRECTORY_SEPARATOR.'[[FILE_NAME]]');?>";*/
      $this->extends_string = "<?php require_once (GENERATED_MODULES.DIRECTORY_SEPARATOR.'[[APP]]'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'[[moduleName]]'.'[[FILE_PATH]]'.DIRECTORY_SEPARATOR.'[[FILE_NAME]]');?>";
      $this->template = "backend/module_extended";
      $this->start_write_path = BACKEND.DIRECTORY_SEPARATOR.'modules';
      
      $this->buildModule();
		}
		elseif ($this->app == 'backend' && !isset($this->tableName))
		{
		  if (!file_exists(BACKEND.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleName)) {
		    $this->template = 'backend'.DIRECTORY_SEPARATOR.'module';
        $this->start_write_path = BACKEND.DIRECTORY_SEPARATOR.'modules';
        $this->buildModule();
		  }
		  else {
		    $this->kill(BACKEND.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleName." Exists. Please remove directory before building module.");
		  }
    }
		elseif ($this->app == 'frontend' && isset($this->tableName))
		{
		  $this->overwrite = 'true';
		  $this->createListFieldsFrontend();
		  $this->frontendFields();
		  $this->build_list_functions();
		  $this->table_with_relations = "Doctrine_Query::create()->from('".$this->tableName." ".$this->table_as."')"
		    .$this->getRelations($this->tableName, $this->table_as)
		    ."->where('".$this->table_as.".id=?'";
		  
		  if (!file_exists($this->generated_dir.DIRECTORY_SEPARATOR.'modules')) { mkdir($this->generated_dir.DIRECTORY_SEPARATOR.'modules'); }
      
      $this->template = "frontend/module_generated";
      $this->start_write_path = $this->generated_dir.DIRECTORY_SEPARATOR.'modules';
      $writepath = $this->start_write_path.DIRECTORY_SEPARATOR.$this->moduleNameGenerated;
      if (!file_exists($writepath)) { mkdir($writepath); }
      // build generated module
      $this->buildModule();
      
      // set overwrite to false and build application module
      $this->overwrite = 'false';
      $this->is_extends = 'true';
      /*$this->extends_string = "<?php require_once ($this->generated_dir_MODULES.DIRECTORY_SEPARATOR.'[[APP]]'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'[[className]]'.'[[FILE_PATH]]'.DIRECTORY_SEPARATOR.'[[FILE_NAME]]');?>";*/
      $this->extends_string = "<?php require_once (GENERATED_MODULES.DIRECTORY_SEPARATOR.'[[APP]]'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'[[moduleName]]'.'[[FILE_PATH]]'.DIRECTORY_SEPARATOR.'[[FILE_NAME]]');?>";
      $this->template = "frontend/module_extended";
      $this->start_write_path = FRONTEND.DIRECTORY_SEPARATOR.'modules';
      
      $this->buildModule();
		}
		elseif ($this->app == 'frontend' && !isset($this->tableName))
		{
		  if (!file_exists(FRONTEND.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleName)) {
        $this->template = 'frontend'.DIRECTORY_SEPARATOR.'module';
        $this->start_write_path = FRONTEND.DIRECTORY_SEPARATOR.'modules';
        $this->buildModule();
      }
      else {
        $this->kill(FRONTEND.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleName." Exists. Please remove directory before building module.");
      }
		}
		else {
		  $this->kill("Please choose 'frontend' or 'backend'");
		}
		// add to AdminResources table with admin access
		if ($this->app == 'backend') {
		  // get administrator id
		  $admin = Doctrine_Core::getTable('UserRoles')->findByName('Administrator');
      $count = Doctrine_Query::create()->from('AclResources ar')->where('ar.name=?', $this->moduleName)->addWhere('ar.application=?', $this->app)->count();
      if ($count == 0) {
        $ar = new AclResources();
        $ar->name = $this->moduleName;
        $ar->application = $this->app;
        $ar->role_id = $admin[0]->id;
        $ar->save();
      }
		}
		$this->status($this->moduleName.' Module ('.$this->controller_name.' controller) Created');
	}
	
	
  private function buildModule($path ='')
  {
    $readpath = realpath(dirname(__FILE__).'/../templates/'.$this->template.DIRECTORY_SEPARATOR.$path);
    $writepath = $this->start_write_path.DIRECTORY_SEPARATOR.$this->moduleNameGenerated.str_replace('INDEX', strtolower($this->controller_name), $path);
    
    if ($path == '' && !is_dir($writepath)) {
      try {
       mkdir($writepath);
      }
      catch (Exception $e) {
        $this->error($e->getMessage());
      }
    }
    if (!is_writable($writepath)) { $this->kill($writepath.' is not writable.'); }
    
    if ($handle = opendir($readpath)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && $file != '.svn' && $file != 'README') {
          if (is_dir($readpath.DIRECTORY_SEPARATOR.$file)) {
            try {
              if ($file == 'INDEX') {
                @mkdir($writepath.DIRECTORY_SEPARATOR.strtolower($this->controller_name));
              }
              else {
                @mkdir($writepath.DIRECTORY_SEPARATOR.$file);
              }
            }
            catch (Exception $e) {
              $this->error($e->getMessage());
            }
           $this->buildModule($path.DIRECTORY_SEPARATOR.$file);
          }
          elseif (is_file($readpath.DIRECTORY_SEPARATOR.$file))
          {
            $filename = $file;
            if ($file == 'CONTROLLER.php') {
              $filename = $this->controller_name.'Controller.php';
            }
            if (!file_exists($writepath.DIRECTORY_SEPARATOR.$filename) || $this->overwrite == 'true')
            {
              if ($this->is_extends == 'true' && $file != 'CONTROLLER.php') {
                $ic = $this->extends_string;
              }
              else {
                $ic = file_get_contents($readpath.DIRECTORY_SEPARATOR.$file);
              }
              $ic = str_replace('[[APP]]', $this->app, $ic);
              $ic = str_replace('[[className]]', $this->moduleNameGenerated, $ic);
  				    $ic = str_replace('[[moduleName]]', $this->moduleName, $ic);
  				    $ic = str_replace('[[moduleNameVar]]', strtolower($this->moduleName), $ic);
  				    $ic = str_replace('[[saveFields]]', $this->updateFromRequestFields, $ic);
  				    $ic = str_replace('[[TABLE]]', $this->tableName, $ic);
  				    $ic = str_replace('[[TABLE_AS]]', $this->table_as, $ic);
              $ic = str_replace('[[edit_vars]]', $this->edit_form_fields, $ic);
              $ic = str_replace('[[update_functions]]', $this->update_functions, $ic);
              $ic = str_replace('[[LIST_DEFAULTS]]', $this->list_defaults, $ic);
              $ic = str_replace('[[LIST_QUERY_FILTERS]]', $this->list_query_filters, $ic);
              $ic = str_replace('[[list_head]]', $this->list_th, $ic);
              $ic = str_replace('[[list_td]]', $this->list_td, $ic);
              $ic = str_replace('[[FILE_PATH]]', str_replace('INDEX', strtolower($this->controller_name), $path), $ic);
              $ic = str_replace('[[FILE_NAME]]', $file, $ic);
              $ic = str_replace('[[INFO]]', $this->info, $ic);
              $ic = str_replace('[[CONTROLLER_NAME]]', $this->controller_name, $ic);
              $ic = str_replace('[[CONTROLLER_VIEW]]', strtolower($this->controller_name), $ic);
              $ic = str_replace('[[TABLE_WITH_RELATIONS]]', $this->table_with_relations, $ic);
              
              #$ic = str_replace('[[list_head]]', $this->list_head, $ic);
            $ic = str_replace('[[list_td]]', $this->list_td, $ic);
            $ic = str_replace('[[FILTERS]]', $this->filters, $ic);
            $ic = str_replace('[[SHOW_FIELDS]]', $this->frontendShow, $ic);
              if ($file == 'CONTROLLER.php') {
                file_put_contents($writepath.DIRECTORY_SEPARATOR.$this->controller_name.'Controller.php',$ic);
              }
              else {
  				      file_put_contents($writepath.DIRECTORY_SEPARATOR.$file,$ic);
              }
            }
          }
        }
      }
    closedir($handle);
    }
  }
  
  
	/**
	 * Remove directory
	 * @param unknown_type $dir
	 * @param unknown_type $virtual
	 * @param unknown_type $filename
	 */
  private function remove_directory($dir, $virtual = false, $filename = '')
  {
    $ds = DIRECTORY_SEPARATOR;
    $dir = $virtual ? realpath($dir) : $dir;
    $dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
    if (is_dir($dir) && $handle = opendir($dir))
    {
      while ($file = readdir($handle))
      {
        if ($file == '.' || $file == '..')
        {
          continue;
        }
        elseif (is_dir($dir.$ds.$file))
        {
          $this->remove_directory($dir.$ds.$file, false, $filename);
        }
        else
        {
          if ($file == $filename)
          {
            unlink($dir.$ds.$file);
          }
        }
      }
      closedir($handle);
      if (count(scandir($dir)) == 2) {
        rmdir($dir);
      }
      return true;
    }
  }
  
	/**
	 * Generates fields use in indexAction update[module]FromRequest()
	 * and generates the update[FieldName] functions in Admin Module Controller Class
	 * 
	 */
	function createUpdateFields()
	{
	  $insertValues = "";
	  $update_functions = "";
	  $table = Doctrine_Core::getTable($this->tableName)->getColumns();
	  
	  foreach ($table as $fielname => $info)
	  {
	    if (strtolower($fielname) != 'id' && strtolower($fielname) != 'created_at' && strtolower($fielname) != 'updated_at')
	    {
	    $insertValues .= "if (isset($".$this->moduleNameVar."['".$fielname."'])) {
	      $".$this->moduleNameVar."_rs->".$fielname." = \$this->update".$this->moduleName.ucfirst($fielname)."($".$this->moduleNameVar."['".$fielname."']);
	    }
	    ";
	    $update_functions .= "
    protected function update".ucfirst($this->moduleNameVar).ucfirst($fielname)."($".$fielname." = '') {
   return  $".$fielname.";
  }";
	    }
	  }
	  
	  $this->update_functions = $update_functions;
	  $this->updateFromRequestFields = $insertValues;
	}

	
	/**
   * Generates list form for _list.phtml on frontend
   * Generates filter form for _list_filter.phtml on frontend
   */
  function createListFieldsFrontend()
  {
    $insertValues = "";
    $table = Doctrine_Core::getTable($this->tableName)->getColumns();
    
    $head = '';
    $td = '';
    $filters = '';
    foreach ($table as $fielnameKey => $info)
    {
    	$fielname = ucwords(str_replace('_', ' ', $fielnameKey));
      if (strtolower($fielnameKey) != 'created_at' && strtolower($fielnameKey) != 'updated_at') {
        $head .= "<th class=\"list\">".ucwords(str_replace('_',' ', $fielname))."</th>\r\n";
        $td .= "<td><a href=\"<?php echo \$this->url(array('module'=>'".$this->moduleName."', 'action'=>'show', 'id'=>\$".strtolower($this->moduleName)."->id));?>\"><?php echo \$".strtolower($this->moduleName)."->".$fielnameKey.";?></a></td>\r\n";
        $filters .= "
          ".ucwords(str_replace('_', ' ', $fielnameKey)).":
          <input type='text' id='filter-".$fielnameKey."' name='filter[".$fielnameKey."]' value='<?php echo \$this->filter['".$fielnameKey."'];?>'><br/>
          ";
      }
    }
    
    $this->list_th = $head;
    $this->list_td = $td;
    $this->filters = $filters;
  }

  function frontendFields()
  {
    $fields = Doctrine_Core::getTable($this->tableName)->getColumns();
    $list = '';
    $show = '';
    $filters = '';
    
    foreach ($fields as $k=>$v)
    {
      $list .= "<a href=\"<?php echo \$this->url(array('module'=>'".$this->moduleName."', 'controller'=>'".$this->controller_name."', 'action'=>'show', 'id'=>\$".strtolower($this->moduleName)."->id));?>\"><?php echo \$".strtolower($this->moduleName)."->".$k.";?></a>\r\n";
      // add filters to list query
      $show .= "
      <?php if (trim(\$this->".strtolower($this->moduleName)."['".$k."']) != '') : ?>
        ".ucwords(str_replace('_', ' ', $k)).": <?php echo \$this->".strtolower($this->moduleName)."['".$k."']; ?><br/>
      <?php endif; ?>
      ";
      $filters .= "
      ".ucwords(str_replace('_', ' ', $k)).":
      <input type='text' id='filter-".$k."' name='filter[".$k."]' value='<?php echo \$this->filter['".$k."'];?>'><br/>
      ";
    }
    $this->frontendList = $list;
    $this->frontendShow = $show;
    $this->filters = $filters;
    
  }
	/**
	 * Generates list for _list.phtml
	 *
	 * @param unknown_type $moduleNameVar
	 * @param unknown_type $tableName
	 * @return unknown
	 */
	function createEditFormFields()
	{
	  $insertValues = "";
	  $table = Doctrine_Core::getTable($this->tableName)->getColumns();
	  
	  $output = '';
	  foreach ($table as $fielname => $info)
	  {
	    if (strtolower($fielname) != 'id' && strtolower($fielname) != 'created_at' && strtolower($fielname) != 'updated_at')
	    {
	    $output .= "<tr>
	    <td width=\"100px\"><label>".ucwords(str_replace('_',' ', $fielname)).":</label></td>
	    <td>";
	    if (strtolower($info['type']) == 'enum') {
	    	$varr = array();
	    	foreach ($info['values'] as $iv) {
	    		$varr[] = "'".$iv."' => '".$iv."'";
	    	}
	      $output .="<?php echo \$this->formSelect(\"".$this->moduleNameVar."[".$fielname."]\", \$this->".$this->moduleNameVar."['".$fielname."'], array(), array(''=>'', ".implode(',', $varr).")); ?>";
	    }else{
	      $output .="<?php echo \$this->formText(\"".$this->moduleNameVar."[".$fielname."]\", \$this->".$this->moduleNameVar."['".$fielname."']); ?>";	
	    }
	    $output .="</td>
	  </tr>\r\n";
	   }
	  }
	  
	  $this->edit_form_fields = $output;
	}
	
  function build_list_functions()
  {
  	$fields = Doctrine_Core::getTable($this->tableName)->getColumns();
  	$listQueryX = '';
  	foreach ($fields as $k=>$v)
  	{
  		$tmp[] = "'".$k."'";
  		// add filters to list query
  		$listQueryX .= "(key_exists('".$k."', \$filter) && trim(\$filter['".$k."'])!='' ? \$doc->addWhere('".$this->table_as.".".$k." LIKE ?', \$filter['".$k."']) : '');
      ";
  	}
  	$fString = implode(',', $tmp);
  	/*
  	// Set Defaults
    $heading = 'Pages';
    $display = array('id', 'statusid', 'title', 'meta_keywords', 'meta_description', 'content', 'url_routingid', 'created_at', 'updated_at');
    $filters = array('id', 'statusid', 'title', 'meta_keywords', 'meta_description', 'content', 'url_routingid', 'created_at', 'updated_at');
    $labels = array('id', 'statusid', 'title', 'meta_keywords', 'meta_description', 'content', 'url_routingid', 'created_at', 'updated_at');
    $resultsPerPage = 20;
  	*/
  	$listDefaults = "
  	// Set Defaults
  	\$heading = '".$this->moduleName."';
  	\$display = array(".$fString.");
  	\$filters = array(".$fString.");
  	\$labels = array(".$fString.");
  	\$resultsPerPage = 20;
  	
  	\$configFile = APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.Zend_Controller_Front::getInstance()->getRequest()->getModuleName().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.yml';
    
    if (file_exists(\$configFile))
    {
      \$config = sfYaml::load(\$configFile);
      \$this->view->ymlConfig = \$config['list'];
      (key_exists('title', \$config['list']) ? \$heading = \$config['list']['title'] : '');
      (key_exists('display', \$config['list']) ? \$display = \$config['list']['display'] : '');
      (key_exists('filters', \$config['list']) ? \$filters = \$config['list']['filters'] : '');
      (key_exists('perpage', \$config['list']) ? \$resultsPerPage = \$config['list']['perpage'] : '');
      \$labels = \$config['labels'];
    }
    
    \$this->view->labels = \$labels;
    \$this->view->display = \$display;
    \$this->view->heading = \$heading;
    \$filter = \$this->_getParam('filter');
    
    if (!\$filter) {
      \$filter = \$sessionNamespace->filter;
    }
    else {
      \$sessionNamespace->filter = \$filter;
    }
    \$filters2 = array();
    if (is_array(\$filters)) {
      foreach (\$filters as \$y)
      {
        \$filters2[\$y] = (is_array(\$filter) && key_exists(\$y, \$filter) ? \$filter[\$y] : '');
      }
    }
    
    \$this->view->filter = \$filters2;
    ";
  	$this->list_defaults = $listDefaults;
  	$this->list_query_filters = $listQueryX;
  }
  /**
   * Generate our query to pull in relations
   * used for show & editActions
   * @param unknown_type $table
   * @param unknown_type $table_as
   */
  private function getRelations($table, $table_as)
  {
    $do_with = array();
    $this->table_aliases[$table_as] = $table_as;
    $relations = Doctrine_Core::getTable($table)->getRelations();
    
    $string = '';
    
    foreach ($relations as $name => $relation) {
      // make table_as
      $tmp = explode(' ', trim(preg_replace('/([A-Z])/', ' $1', $name)));
      $as = '';
      foreach ($tmp as $t) {
        $as .= $t[0];
      }
      $table_as2 = strtolower($as);
      
      if (in_array($table_as2, $this->table_aliases))
      {
        $count = 2;
        while (in_array($table_as2.$count, $this->table_aliases))
        {
          $count++;
        }
        $table_as2 .= $count;
      }
      
      if (!in_array($name, $this->tables)) {
        $do_with[] = array('table_as'=>$table_as2, 'name'=>$name);
        $this->tables[$table] = $table; // keep track of tables we already have
        $this->tables[$name] = $name;
        $string .="
      ->leftJoin('".$table_as.".".$name." ".$table_as2."')";
      }
    }
    
    foreach ($do_with as $d)
    {
      $string .= $this->getRelations($d['name'], $d['table_as']);
    }
    
    return $string;
  }
  
  private function error($text) {
    echo "\033[31m\033[1m".$text."\033[40;37m\r\n";
  }
  private function kill($text) {
    $this->error($text);
    die();
  }
  private function status($text) {
    echo "\033[0;34m\033[1m".$text."\033[40;37m\r\n";
  }
  private function camelCase($text)
  {
    if (substr_count($text, '_') > 0) {
      $temp = explode('_', $text);
      if (is_array($temp)) {
       $text = str_replace(' ', '', ucwords(implode(' ', $temp)));
      }
      else {
        $text = str_replace(' ', '', ucwords($temp));
      }
    }
    else {
      $text = str_replace(' ', '', ucwords($text));
    }
    
    return $text;
  }
  private function help()
  {
    if (file_exists(realpath(dirname(__FILE__).'/../README'))) {
      echo $help = file_get_contents(realpath(dirname(__FILE__)).'/../README');
    }
    else {
      $this->error("Sorry, ".realpath(dirname(__FILE__).'/../README'). ' was not found.');
    }
    echo "
";
    die();
  }
  
  private function info()
  {
    $this->info = "/**
 * Generated by Smallunch Framework ".date('m/d/Y g:i:s A')."
 * http://www.smallunch.com by Jeremy Moore
 * Boe Technologies llc boe-technologies@gmail.com
 *
 */";
  }
}