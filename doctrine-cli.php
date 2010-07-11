#!/usr/bin/env php
<?php
define('PROJECT_LOCATION', realpath(dirname(__FILE__)));
require_once(realpath(dirname(__FILE__)).'/library/Smallunch/lib/scripts/includes/bootstrap_doctrine.php'); 
$cli = new Doctrine_Cli($application->getOption('doctrine'));
$cli->run($_SERVER['argv']);

