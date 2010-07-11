<?php
defined('APPLICATION_ENV') or define('APPLICATION_ENV', 'production');
defined('APPLICATION_DIRECTORY') or define('APPLICATION_DIRECTORY', dirname(__FILE__));

defined('LIBRARY_DIRECTORY') or define('LIBRARY_DIRECTORY', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'library'));
define('SMALLUNCH_DIRECTORY', LIBRARY_DIRECTORY.DIRECTORY_SEPARATOR.'Smallunch');


require SMALLUNCH_DIRECTORY.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'global_bootstrap.php';

// Load custom routes
require_once APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routes.php';
$application->bootstrap()->run();