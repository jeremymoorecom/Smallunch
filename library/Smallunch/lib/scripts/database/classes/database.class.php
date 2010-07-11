<?php
require_once(realpath(dirname(__FILE__)).'/../../includes/bootstrap_doctrine.php');
 
class database
{
  public $table_name;
  public $zend_console;
  public $argv;
  
  public function __construct($argv)
  {
    $this->argv = $argv;
    $this->zend_console = new Zend_Console_Getopt(
      array(
        'view|v'        => 'View database settings',
        'test|t-w'      => 'Test database connection to table (-t Users)',
        'create|c'      => 'Create database configureation',
        'adapter|a-w'   => 'Database adapter (mysql) - Required for Create',
        'host|h-w'      => 'Default to localhost - Required for Create',
        'username|u-w'  => 'Username to connect to database - Required for Create',
        'password|p-w'  => 'Password to connect to database - Required for Create',
        'database|d=w'  => 'Database Name - Required for Create',
        'port|o-i'      => 'Port for connecting to database - optional for Create',
        'socket|s-w'    => 'Location for database socket - optional for Create'
        
      ),
      $this->argv
    );
    
    try {
      if (isset($this->zend_console->v)) {
          $this->viewConfig();
      }
      elseif (isset($this->zend_console->t)) {
          $this->table_name = $this->zend_console->getOption('t');
          $this->testConnection();
      }
      elseif (isset($this->zend_console->c)) {
         $this->configure();
      }
    } catch (Zend_Console_Getopt_Exception $e) {
        echo $e->getUsageMessage();
        exit;
    }
  }
  private function testConnection()
  {
    try {
    $users = Doctrine_Core::getTable($this->table_name)->find(1);
    fwrite(STDOUT, "Connection Successful\n");
    }
    catch (Exception $e) {
      fwrite(STDOUT, "Connection Failed\n");
      fwrite(STDOUT, $e->getMessage()."\n");
    }
  }
  
  private function viewConfig()
  {
    $config = new Zend_Config_Ini(SMCONFIG_DIRECTORY.DIRECTORY_SEPARATOR.'database.ini', 'database');
    fwrite(STDOUT, "Adapter: ".$config->db->doctrine_adapter."\n");
    fwrite(STDOUT, "Username: ".$config->db->params->username."\n");
    fwrite(STDOUT, "Password: ".$config->db->params->password."\n");
    fwrite(STDOUT, "Host: ".$config->db->params->host."\n");
    fwrite(STDOUT, "Port: ".$config->db->params->port."\n");
    fwrite(STDOUT, "Database: ".$config->db->params->dbname."\n");
    fwrite(STDOUT, "Socket: ".$config->db->params->socket."\n");
    exit(0);
  }
  private function configure()
  {
    $zend_console = new Zend_Console_Getopt(
      array(
        'create|c'      => 'Create database configureation',
        'adapter|a=w'   => 'Database adapter (mysql)',
        'host|h=w'      => 'Default to localhost',
        'username|u=w'  => 'Username to connect to database',
        'password|p=w'  => 'Password to connect to database',
        'database|d=w'  => 'Database Name',
        'port|o-i'      => '(optional) Port for connecting to database',
        'socket|s-w'    => '(optional) Location for database socket'
        
      ),
      $this->argv
    );
    try {
      echo $a = $zend_console->getOption('a');
      echo $h = $zend_console->getOption('h');
      
        // Load all sections from an existing config file, while skipping the extends.
        $config = new Zend_Config_Ini(SMCONFIG_DIRECTORY.DIRECTORY_SEPARATOR.'database.ini',
                              null,
                              array('skipExtends'        => true,
                                    'allowModifications' => true));
 
        // Modify values
        $config->database->doctrine_adapter  = $zend_console->getOption('a');
        $config->database->params->host      = $zend_console->getOption('h');
        $config->database->params->username  = $zend_console->getOption('u');
        $config->database->params->password  = $zend_console->getOption('p');
        $config->database->params->dbname    = $zend_console->getOption('d');
        $config->database->params->port      = $zend_console->getOption('h');
        $config->database->params->socket    = $zend_console->getOption('h');
        // Write the config file
        $writer = new Zend_Config_Writer_Ini(array('config'   => $config,
                                                   'filename' => 'config.ini'));
        $writer->write();
        //*/
    } catch (Zend_Console_Getopt_Exception $e) {
      fwrite(STDOUT, "Connection Failed\n");
        echo $e->getUsageMessage();
        exit;
    }
  }
}