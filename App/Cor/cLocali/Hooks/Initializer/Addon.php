<?php
/**
 * This hook must register in very top of application
 * 
 * Target: 'Candoo' Event: 'onAppRun'
 * 
 * @author root
 */
class cLocali_Hooks_Initializer_Addon extends Candoo_Addon_Abstract
{
	public function onAppRunAction() 
	{
	    $this->setNoRender();
	    
	    // init cLocali module, ghabl az inke be ghesmat e inital beresim
	    $inital = new cLocali_Init();
	    $inital ->init();
	    
	    // reset internal cache of config, baraaie inke az in pas config raa bar asaase locale morede nazar bekhaanad
	    Candoo_Config::resetInternalCache();
	}
}
