<?php
ini_set('display_errors', 'on');
define('APPLICATION_ENV', 'development');
define('APP', 'backend');
// Set application directory
define('APPLICATION_DIRECTORY', realpath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'backend'));

require (realpath(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php'));
