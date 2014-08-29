<?php
class cLocali_Lib_Translate extends Zend_Translate_Adapter_Array
{
    /**
     * 
     * @param string $source Mitavaanad naame yek module yaa masire folder e marboot be translation baashad
     */
	public function __construct($source = null)
	{
	    /**
	     * @note
	     * 
	     * Aval translate bar asaase file haaye translate e default(cLocalization)
	     * construct mishavad va sepas file haaye module|path e konooni ezaafe mishavad
	     * be in shekl aval module raa baraaie translate migardad agar movafagh nabood 
	     * be default(cLocalization miravad)
	     */
	    
	    $filePath = APP_DIR_CORE .DS. 'cLocali' .DS. 'Langs';  	    
	    /*
	     * @arg1 adapter
	     * @arg2 content | mitavaanad shaamele masire file array baashad yaa yek array haavie key=>trans
	     * @arg3 locale: | agar khaali baashad yaa 'auto' az rooie Zend_Registery=>Zend_Locale migirad
	     */ 
	    parent::__construct(array(
	    	//'adapter' => 'Array',
	    	'content' => $filePath,
	    	/* there is a bug in Zend_Translation_Adapter_Array
	    	 * if locale set to auto in ->translate() return 'auto' as locale 
	    	 * and translate do`nt work currectly
	    	 * agar nabaashad locale ro khodesh tashkhis mide
	    	'locale'  => 'auto',
	    	*/
	    	// filename as locale
	    	'scan' 	  => self::LOCALE_FILENAME,
	    	// disable triggered error notice, on not found languages resource
	    	// exp No translation for the language 'en' available 
	    	'disableNotices' => true,
	    ));
	    
	    // ````````````````````````````````````````````````````````````
	    if (!$source) {
	        // use current module name after dispatch
	        $source = Candoo_App_Resource::get('request')->getModuleName();
	    }
	    
	    // emkaan daarad ghabl az dispatch estefaade shavad
	    if ($source) {
	    	$this->addTranslation($source);
	    }

	}

	/**
	 * @parent rewritten
	 * 
	 * @param string $key
	 */
	public function translate($messageId,$locale = null)
	{
		$lmessageId = strtolower($messageId);		
		$return = parent::translate($lmessageId,$locale);
		if (strtolower($messageId) == $return) {
		    $return = $messageId;
		}
		
		if (!$this->isTranslated($lmessageId)) {
		    //$return = $this->autoTranslate($messageId,$locale);
		}
		
		return $return;
	}
	
	/**
	 * Mitavaanad $options baraabar baa naaame yek module baashad
	 * pas az in mitavan az file haaye aan module ham estefaade kard
	 * 
	 * @see Zend_Translate_Adapter::addTranslation()
	 */
	public function addTranslation($options = array())
	{
	    if (is_string($options) && !empty($options) && $options != 'cLocali') {
	    	if (Candoo_Module::isCoreModule($options)) {
	    		$options = APP_DIR_CORE .DS. $options .DS. 'Langs';
	    	} elseif (Candoo_Module::isInstalledModule($options)) {
	    		$options = APP_DIR_MODULES .DS. $options .DS. 'Langs';
	    	}
	    }
	    
	    if (!is_array($options)) {
	        if (!is_dir($options)) {
	        	return $this;
	        }    
	    }
	    
	    parent::addTranslation($options);
	    
	    return $this;
	}
	
	
}
