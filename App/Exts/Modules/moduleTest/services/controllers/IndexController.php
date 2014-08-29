<?php
class moduleTest_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
	    echo 'this is indexAction service test of moduleTest';
	    exit;
	}
	
	public function testAction()
	{
	    echo 'this is test';
	    exit;
	}
}