<?php
class Candoo_Cache_File
{
    // cache object
    protected $_cache;
    
    protected $_id;
    
    const TAG_NAME = 'TAG_CANDOO_CACHE_FILES';
    
    /**
     * 
     * @param string $file filepath to file
     * @param array $idDepen  hengaame tolide tag haa az in parameter haa estefaade mikonad
     * 						  masalan agar yek file ro mikhaanim ke marboot be config haast
     * 						  mitavaan enviorement haaye mokhtalefi ro az aaan load kard
     */
    public function __construct($file,$idDepen = array())
    {
        // get backend cache engine from Candoo_App_Config
        // caching work only when programm setup
        if (! Candoo_Cache::isEnabled()) {
        	throw new Exception('You cant use cache until Candoo_App->setup() run; or chache is disabled by config');    
        }
                 
        $cache  = Zend_Cache::factory('File', 'File',
        		array(
        				'master_files' => array($file),
        				'automatic_serialization' => true,
        				'lifetime' => 7200,
        				'caching'  => true
        		),
        		array('cache_dir' => APP_DIR_TEMP.DS.'Cache')
        );
        
        $this->_cache = $cache;
        
        // create ID for this cache object
        $this->_id = md5($file.serialize($idDepen)); 
    }
        
    public function load()
    {
        $cache = $this->_cache;
        
        return $cache->load($this->_id);
    }
    
    public function save($data,$tags = array())
    {
        $cache = $this->_cache;
        
        if (empty($tags)) {
            $tags = array(self::TAG_NAME);
        }
        
        return $cache->save($data,$this->_id,$tags);
        
    }
}