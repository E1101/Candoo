<?php
class cLocali_Helpers_View_Locale extends Zend_View_Helper_Abstract
{
    public function locale()
    {
        return $this;
    }
    
    /**
     * 
     * @return string 'en_US'
     */
    public function getLocale()
    {
        return $this->__toString();
    }
    
    /**
     * @return string 'en'
     */
    public function getLang()
    {
        return $this->getIns()->getLanguage();
    }
    
    /**
     * @return string 'US' is available
     */
    public function getRegion()
    {
        return $this->getIns()->getRegion();
    }
    
    /**
     * return direction of language script 
     * 
     * @return string 'ltr' | 'rtl'
     */
    public function getHttpDir()
    {
        $layout = Zend_Locale_Data::getList($this->getIns(), 'Layout','en');
        
        return ($layout['characters'] == 'right-to-left') ? 'rtl' : 'ltr';
    }
    
    /**
     * is current locale language script right to left?
     * 
     * @return boolean
     */
    public function isRtl()
    {
        return ($this->getHttpDir() == 'rtl');
    }
    
    public function getHttpCharset()
    {
        return $this->getIns()->getHttpCharset();
    }
    
    
    public function __call($method,$args)
    {
        // ->convert[Script]Numerals($number)
        if (substr($method, 0,7) == 'convert' && substr($method, -8,8) == 'Numerals') {
            $method = str_replace('convert',  '', $method);
            $method = str_replace('Numerals', '', $method);
            $method = ucfirst(strtolower($method));
            
            // map to this method
            return $this->convertNumerals($args[0],'Latin',$method);
        }
    }
    
     /**
     * Changes the numbers/digits within a given string from one script to another
     * 'Decimal' representated the stardard numbers 0-9, if a script does not exist
     * an exception will be thrown.
     *
     * Examples for conversion from Arabic to Latin numerals:
     *   convertNumerals('١١٠ Tests', 'Arab'); -> returns '100 Tests'
     * Example for conversion from Latin to Arabic numerals:
     *   convertNumerals('100 Tests', 'Latn', 'Arab'); -> returns '١١٠ Tests'
     *
     * @param  string  $input  String to convert
     * @param  string  $from   Script to parse, see {@link Zend_Locale::getScriptList()} for details.
     * @param  string  $to     OPTIONAL Script to convert to
     * @return string  Returns the converted input
     */
    public function convertNumerals($number,$from,$to = null) 
    {
        $from = $this->_getScriptCode($from);
        if (isset($to)) {
            $to = $this->_getScriptCode($to);
        }
        
        return Zend_Locale_Format::convertNumerals($number, $from, $to);
    }
    
    // getScriptCode('Latin', 'en'); // outputs "Latn"
    protected function _getScriptCode($scriptName)
	{
    	$scripts2names = Zend_Locale_Data::getList('en', 'script');
    	$names2scripts = array_flip($scripts2names);
    	return (isset($names2scripts[$scriptName])) ? $names2scripts[$scriptName] : $scriptName ;
	}
    
    public function __toString()
    {
        return (string) $this->getIns();
    }
    
    protected function getIns()
    {
        return Candoo_App_Resource::get('locale');
    }
}
