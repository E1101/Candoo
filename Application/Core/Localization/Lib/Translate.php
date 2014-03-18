<?php
class Localization_Lib_Translate extends Zend_Translate_Adapter_Array
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
	     * Aval translate bar asaase file haaye translate e default(Localization)
	     * construct mishavad va sepas file haaye module|path e konooni ezaafe mishavad
	     * be in shekl aval module raa baraaie translate migardad agar movafagh nabood 
	     * be default(Localization miravad)
	     */
	    
	    $filePath = APP_DIR_CORE .DS. 'Localization' .DS. 'Langs';  	    
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
	    
	    if (!empty($source) && $source != 'Localization') {
	        if (Candoo_Extension_Module::isCoreModule($source)) {
	        	$filePath = APP_DIR_CORE .DS. $source .DS. 'Langs';
	        } elseif (Candoo_Extension_Module::isInstalledModule($source)) {
	        	$filePath = APP_DIR_MODULES .DS. $source .DS. 'Langs';
	        } elseif (is_dir($source)) {
	        	$filePath = $source;
	        }
	         
	        $this->addTranslation($filePath);
	    }
	}

	/**
	 * @parent rewritten
	 * 
	 * @param string $key
	 */
	public function translate($messageId,$locale = null)
	{	    
		$messageId = strtolower($messageId);
		$return = parent::translate($messageId,$locale);
		
		if (!$this->isTranslated($messageId)) {
		    //$return = $this->autoTranslate($messageId,$locale);
		}
		
		return $return;
	}
}
