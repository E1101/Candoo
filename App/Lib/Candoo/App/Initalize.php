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
        $coreModules = Candoo_Module::getCoreModules();
        $insModules  = Candoo_Module::getActiveModules();
        
        /* Module haa bar asaase name va be tartib az core be installed module ejraa mishavand */
        sort($coreModules);
        sort($insModules);
        /**
         * Module haaye core ba`d az module haaye nasb shode init mishavand
         * be in shkel shoma masalan nemitavind route haaye pishfarz va yaa
         * harchiz e digari az core raa jaaigozin konid ke baraaie system
         * hayaati ast mesle dastresi be route e admin va ...
         */
        $modules = array_merge($insModules,$coreModules);
                        
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