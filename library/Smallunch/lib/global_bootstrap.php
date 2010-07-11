<?php
require_once (realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.php'); 

set_include_path('.' . PATH_SEPARATOR . LIBRARY_DIRECTORY 
    . PATH_SEPARATOR . SM_RESOURCE 
    . PATH_SEPARATOR . LIBRARY_DIRECTORY . DIRECTORY_SEPARATOR . 'Zend' 
    . PATH_SEPARATOR . LIBRARY_DIRECTORY . DIRECTORY_SEPARATOR . 'Doctrine' 
    . PATH_SEPARATOR . get_include_path());

/** Zend_Application */
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config'.DIRECTORY_SEPARATOR.'application.ini');

$frontController = Zend_Controller_Front::getInstance();
// Add Modules Directories
$frontController->addModuleDirectory(SM_LIB.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.APP);
$frontController->addModuleDirectory(SMALLUNCH_DIRECTORY.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.APP);
$frontController->addModuleDirectory(APPLICATION_DIRECTORY.DIRECTORY_SEPARATOR."modules");
