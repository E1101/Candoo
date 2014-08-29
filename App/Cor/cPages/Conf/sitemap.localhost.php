<?php 
return array(
	array (
		'label'  => 'Home',
		'module' => 'moduleTest',
		'controller' => 'index',
		'action' => 'index',
		'params' => array(
			'param1' => 'value1',
		),
		'route' => 'home', // system readable of label
		'router' => array (
			'type' => 'Zend_Controller_Router_Route_Static',
			'route' => '/',
		),
		'pages' => array(
			array(
				'label' => 'Sub Home',
				'module' => 'moduleTest',
				'controller' => 'index',
				'action' => 'sub',
				'route' => 'sub_home',
				'router' => array (
					'type' => 'Zend_Controller_Router_Route_Static',
					'route' => '/home/sub',
				),
			),
		),
	),
	array (
		'label'  => 'Test',
		'module' => 'moduleTest',
		'controller' => 'index',
		'action' => 'test',
		'route' => 'test',
		'visible' => false, //regex routes not visible (visibility cause error trigired)
		'router' => array (
				'type' => 'Zend_Controller_Router_Route_Regex',
				'route' => 'test/(\d+)-(\w+).html',
				'reverse' => 'test/%d-%s.html',
				'map' => array(1 => 'id',2 => 'title'), 
		),
	),
	array (
		'label'  => 'Zend',
		'uri' => 'http://www.zend-project.com/',
	),
);