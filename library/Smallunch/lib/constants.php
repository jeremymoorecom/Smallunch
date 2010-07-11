<?php
defined('ROOT_DIRECTORY') || define('ROOT_DIRECTORY', realpath(dirname(__FILE__) . '/../../../'));
defined('APPLICATION_DIRECTORY') || define('APPLICATION_DIRECTORY', realpath(dirname(__FILE__) . '/../../../application'));
defined('SMALLUNCH_DIRECTORY') || define('SMALLUNCH_DIRECTORY', realpath(dirname(__FILE__) . '/../'));
defined('LIBRARY_DIRECTORY') || define('LIBRARY_DIRECTORY', realpath(dirname(__FILE__) . '/../../'));
defined('SMCONFIG_DIRECTORY') || define('SMCONFIG_DIRECTORY', realpath(dirname(__FILE__) . '/../../../application/config'));
// global application helper dir
defined('GLOBAL_HELPER_DIR') || define('GLOBAL_HELPER_DIR', realpath(dirname(__FILE__) . '/../../../application/Helper'));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
define('SM_LIB', SMALLUNCH_DIRECTORY.DIRECTORY_SEPARATOR.'lib');
define('SM_RESOURCE', SM_LIB.DIRECTORY_SEPARATOR.'resources');
// doctrine settings
define('DATA_FIXTURES_PATH', APPLICATION_DIRECTORY.'/doctrine/data/fixtures');
define('MODELS_PATH', ROOT_DIRECTORY.'/models');
define('MIGRATIONS_PATH', APPLICATION_DIRECTORY.'/doctrine/migrations');
define('SQL_PATH', APPLICATION_DIRECTORY.'/doctrine/data/sql');
define('YAML_SCHEMA_PATH', APPLICATION_DIRECTORY.'/doctrine/schema');

define('GENERATED_MODULES', SMALLUNCH_DIRECTORY . DIRECTORY_SEPARATOR . 'generated');
define('DOCTRINE_PATH', APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'doctrine');
define('MODELS_GENERATED_PATH', MODELS_PATH . DIRECTORY_SEPARATOR . 'generated');