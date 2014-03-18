<?php
error_reporting(E_ALL ^ E_NOTICE);

// Define Consts
define('DS', DIRECTORY_SEPARATOR);

define('APP_FRONTEND', 'Web'); // folder-i- ke chizhaaye marboot be site injaa gharaar migirad

// included from Root/Application/Lib/Candoo
define('APP_DIR_ROOT'       , realpath(dirname(__FILE__)) );
define('APP_DIR_APPLICATION', APP_DIR_ROOT .DS. 'Application');
define('APP_DIR_LIBRARIES',   	APP_DIR_APPLICATION .DS. 'Lib');
//define('APP_DIR_CONFIG', 		APP_DIR_APPLICATION .DS. 'Conf');
define('APP_DIR_CORE', 			APP_DIR_APPLICATION .DS. 'Core');
define('APP_DIR_EXTENSION', 	APP_DIR_APPLICATION .DS. 'Extension');
define('APP_DIR_MODULES', 			APP_DIR_EXTENSION 	.DS. 'Modules');
define('APP_DIR_TEMP', 			APP_DIR_APPLICATION .DS. 'Tmp');
define('APP_DIR_CACHE', 			APP_DIR_TEMP .DS. 'Cache');

define('APP_DIR_FRONTEND', APP_DIR_ROOT .DS. APP_FRONTEND);
define('APP_DIR_ASSETS',   	APP_DIR_FRONTEND .DS. 'Assets');
define('APP_DIR_DATA', 		APP_DIR_FRONTEND .DS. 'Data');

// be include path baaz ham ezaafe misheh, dar Candoo_App::initAutoload();
set_include_path(
	PATH_SEPARATOR . APP_DIR_LIBRARIES
   .PATH_SEPARATOR . APP_DIR_APPLICATION
   .PATH_SEPARATOR . get_include_path()
);

// ```````````````````````````````````````````````````````````````````````````````````````````````````````

function echo_microtime() {
	static $time;

	if (isset($time)) {
		$new_time = microtime(true);				
	    $time = ($new_time - $time);
		
		echo '<div style="clear:both;font-family:tahoma;width:100%;line-height:40px;font-size:16px;color:#fff;background-color:blue"> Running Proccess time > '.$time.'ms</div>';
	} else {
		$time = microtime(true);
	}	
}
echo_microtime();
register_shutdown_function('echo_microtime');

// Running Proccess time > 0.061204195022583ms

include_once('Candoo'.DS.'App.php');

try 
{
    $env = getenv('APP_ENV') ? getenv('APP_ENV') : Candoo_App::$ENV_PRODUCTION;
    
	$app = Candoo_App::getInstance()
		 ->setup($env)
		 ->run();
}
catch (Exception $e) 
{
	echo '<pre>';
	echo '<h2>It`s seems be an error</h2>';
	echo '<p style="color:red;font-weight:bold;">'.$e->getMessage().'</p>';
	
	throw $e;
}
