#!/usr/bin/env php
<?php
if (!array_key_exists('1', $_SERVER['argv'])) {
  exit(0);
}
// build list of passed parameters to pass to called script
$argv = array();
foreach ($_SERVER['argv'] as $key => $param) {
  if ($key != 0 && $key != 1) {
    $argv[] = $param;
  }
}
// location of executing script
$project_location = realpath(dirname(__FILE__));
// set some globals
define('PROJECT_LOCATION', $project_location);
define('FRONTEND', PROJECT_LOCATION.'/application/frontend');
define('BACKEND', PROJECT_LOCATION.'/application/backend');
$script = PROJECT_LOCATION.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Smallunch'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.$_SERVER['argv'][1].DIRECTORY_SEPARATOR.$_SERVER['argv'][1];

// check if called script exists
if (!file_exists($script))
{
  echo "\033[31m\033[1m".$_SERVER['argv'][1]." not found\033[40;37m\r\n";
  exit(0); 
}
require_once ($script);
exit(0); 