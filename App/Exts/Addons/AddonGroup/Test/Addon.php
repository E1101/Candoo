<?php
class Addons_AddonGroup_Test_Addon extends Candoo_Addon_Abstract
{
	protected function displayAction($content) 
	{
	    $content .= '|'. $this->getParam('content') .'|';
	    $content = '<<- '. $content .'->>';
	    
	    $this->_view->assign('content',$content);
	}
	
}
