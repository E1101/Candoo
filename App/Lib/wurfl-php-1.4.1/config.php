<?php
$resourcesDir            = dirname(__FILE__) . '/_data/';

$wurfl['main-file']      = $resourcesDir  . 'wurfl-2.0.27.zip';
$wurfl['patches']        = array($resourcesDir . 'web_browsers_patch.xml');

$persistence['provider'] = 'file';
$persistence['dir']      = $resourcesDir . 'cache/';

$cache['provider']       = null;

$configuration['wurfl']       = $wurfl;
$configuration['persistence'] = $persistence;
$configuration['cache']       = $cache;