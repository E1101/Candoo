<?php

error_reporting(E_ALL);

// Define Consts
define('DS', DIRECTORY_SEPARATOR);

define('APP_FRONTEND', 'www'); // folder-i- ke chizhaaye marboot be site injaa gharaar migirad

// included from Root/Application/Lib/Candoo
define('APP_DIR_ROOT'       , realpath(dirname(__FILE__)) );
define('APP_DIR_APPLICATION', APP_DIR_ROOT .DS. 'App');
define('APP_DIR_LIBRARIES',   	APP_DIR_APPLICATION .DS. 'Lib');
//define('APP_DIR_CONFIG', 		APP_DIR_APPLICATION .DS. 'Conf');dastresi tavasote class e config
define('APP_DIR_CORE', 			APP_DIR_APPLICATION .DS. 'Cor');
define('APP_DIR_EXTENSION', 	APP_DIR_APPLICATION .DS. 'Exts');
define('APP_DIR_MODULES', 			APP_DIR_EXTENSION 	.DS. 'Modules');
define('APP_DIR_TEMP', 			APP_DIR_APPLICATION .DS. 'Tmp');
//define('APP_DIR_CACHE', 			APP_DIR_TEMP .DS. 'Cache');

define('APP_DIR_FRONTEND', APP_DIR_ROOT .DS. APP_FRONTEND);
define('APP_DIR_ASSETS',   	APP_DIR_FRONTEND .DS. 'assets');
define('APP_DIR_DATA', 		APP_DIR_FRONTEND .DS. 'data');

// be include path baaz ham ezaafe misheh, dar Candoo_App::initAutoload();
set_include_path(
		 PATH_SEPARATOR . APP_DIR_LIBRARIES
		.PATH_SEPARATOR . APP_DIR_APPLICATION
		.PATH_SEPARATOR . get_include_path()
);
// ```````````````````````````````````````````````````````````````````````````````````````````````````````

include_once(APP_DIR_APPLICATION .DS. 'Autoloader.php');

try
{
    // use caching
    //Candoo_Cache::setEnabled(true);
    
	$app = Candoo_App::getInstance()
		 ->run();
	
} catch (Exception $e) {
	echo '<pre>';
	echo '<h2>It`s seems be an error</h2>';
	echo '<p style="color:red;font-weight:bold;">'.$e->getMessage().'</p>';
	
	throw $e;
}
