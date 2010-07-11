<?php
ini_set('display_errors', 'off');
define('APPLICATION_ENV', 'production');
define('APP', 'frontend');
// Set application directory
define('APPLICATION_DIRECTORY', realpath('..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'frontend'));

require (realpath(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php'));
