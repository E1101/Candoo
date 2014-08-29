<?php

include_once 'Zend/Loader/Autoloader.php';
$autLoader = Zend_Loader_Autoloader::getInstance();
// don`t show warning for including not founded class
// @todo why we should use this ? agar az in estefaade nakonim warning haaye autoloader raa dar khorooji daarim
$autLoader ->suppressNotFoundWarnings(true);

// Using of library classes
$autLoader->registerNamespace('Candoo_');
$autLoader->registerNamespace('Util_');

// Using Extension inside App/Extension folder
set_include_path(PATH_SEPARATOR. APP_DIR_EXTENSION .PATH_SEPARATOR. get_include_path());
$autLoader->registerNamespace('Addons_');
$autLoader->registerNamespace('Helpers_');
$autLoader->registerNamespace('Plugins_');

// Module`s autoload
// exp. new myModule(); everywhere in your code
set_include_path(PATH_SEPARATOR. APP_DIR_CORE .PATH_SEPARATOR. get_include_path());
$coreModules = Candoo_Module::getCoreModules();
foreach ($coreModules as $module) {
	$autLoader->registerNamespace($module.'_');
}

set_include_path(PATH_SEPARATOR. APP_DIR_MODULES .PATH_SEPARATOR. get_include_path());
$modules = Candoo_Module::getInstalledModules();
foreach ($modules as $module) {
	$autLoader->registerNamespace($module.'_');
}