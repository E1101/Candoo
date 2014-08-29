<?php
class Candoo_Cache_Core extends Zend_Cache_Core
{
    // cache object
    protected $_cache;
    
    protected $_id;
    
    const TAG_NAME = 'TAG_CANDOO_CACHE_CORE';
    
    /**
     * 
     * @param string $file filepath to file
     * @param array $idDepen  hengaame tolide tag haa az in parameter haa estefaade mikonad
     * 						  masalan agar yek file ro mikhaanim ke marboot be config haast
     * 						  mitavaan enviorement haaye mokhtalefi ro az aaan load kard
     */
    public function __construct()
    {
        // get backend cache engine from Candoo_App_Config
        // caching work only when programm setup
        if (! Candoo_Cache::isEnabled()) {
        	throw new Exception('You cant use cache until Candoo_App->setup(); Or maybe chache is disabled by config');    
        }
               	
        parent::__construct(array(
        				'lifetime' => 7200,
        				'automatic_serialization' => true,
        				'caching'  => true
       	));
        
        $backendClass = 'Zend_Cache_Backend_'.'File';
        $backendClass = new $backendClass(array('cache_dir' => APP_DIR_TEMP.DS.'Cache'));
        $this->setBackend($backendClass);
    }
    
}