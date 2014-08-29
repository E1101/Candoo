<?php
class Addons_AddonGroup_Boarder_Addon extends Candoo_Addon_Abstract
{
	public function displayAction($content) 
	{
	    $this->setNoRender();
	    
	    $content = '|----------------------------------------------------|'
	    		 . $content
	    		 . '|----------------------------------------------------|'
	    		 ;
	    		 
	   return $content;
	}
	
}
