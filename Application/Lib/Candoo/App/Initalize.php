<?php
class Candoo_App_Initalize
{
    static function init()
    {
    	include_once APP_DIR_CORE.DS.'Init.php';
    	
    	$init = new Core_Init();
    	$init->init();
    }
    
    static function initModules()
    {
        $coreModules = Candoo_Extension_Module::getCoreModules();
        $insModules  = Candoo_Extension_Module::getInstalledModules();
        
        /* Module haa bar asaase name va be tartib az core be installed module ejraa mishavand */
        sort($coreModules);
        sort($insModules);
        $modules = array_merge($coreModules,$insModules);
                        
        foreach ($modules as $mod) {
            $initClass = $mod.'_Init';
            
            if (class_exists($initClass,true)) {
                $initClass = new $initClass();
                
                if (! $initClass instanceof Candoo_App_Initalize_Abstract) {
                    throw new Zend_Exception('Module initalize class must instance of Candoo_App_Initalize_Abstract');
                }
                
                // initalize module
                $initClass ->init();
            }
        }
    }

    
}