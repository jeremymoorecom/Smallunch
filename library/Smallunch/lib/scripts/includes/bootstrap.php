<?php
/**
 * Include this file in to bootstrap.
 * Required if you need access to Doctrine
 */ 
include (PROJECT_LOCATION.'/library/Smallunch/lib/constants.php');
// Set include paths
set_include_path('.' . PATH_SEPARATOR . LIBRARY_DIRECTORY 
    . PATH_SEPARATOR . SM_RESOURCE 
    . PATH_SEPARATOR . LIBRARY_DIRECTORY . DIRECTORY_SEPARATOR . 'Zend' 
    . PATH_SEPARATOR . LIBRARY_DIRECTORY . DIRECTORY_SEPARATOR . 'Doctrine' 
    . PATH_SEPARATOR . get_include_path());
// Bootstrap
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, SM_LIB . '/config/application.ini');
$application->getBootstrap()->bootstrap('cli');