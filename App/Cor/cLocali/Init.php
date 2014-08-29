<?php
class cLocali_Init extends Candoo_App_Initalize_Abstract
{
    public function _initInital()
    {                
        $configs = $this->getConfig();
        
        // register locale based on browser,server detection
        try
        {
            $locale = new Zend_Locale('auto');
            
            $lc = $locale->toString();

            //            [fa]_IR vvvvvvvvvvvvvvvvv
            if (! in_array($lc, explode(',', $configs->locale->languages))  ) {
                $lc = (!empty($configs->locale->restricted)) 
                	? $configs->locale->restricted
                	: $configs->locale->default;
            } else {
                $lc = $configs->locale->default;
            }
        } 
        // agar ghaaader be tashkhis nabood meghdaar pishfarz raa gharaar midahad
        catch (Zend_Locale_Exception $e) 
        {
            $lc = (!empty($configs->locale->restricted)) 
                	? $configs->locale->restricted
                	: $configs->locale->default;
        }
        
        // test locale for valid
        Zend_Locale::isLocale($lc);

        /* Candoo_App_Resource::set('locale' ... az yek setter estefaade mikonad ke locale ro dar Zend_Registery
         * baa kelid e Zend_Locale sabt mikonad
         * */
        $locale->setLocale($lc);

        if (Candoo_Cache::isEnabled()) {
            // $locale use a tunel to Zend_Locale_Data::setCache
        	$locale->setCache(new Candoo_Cache_Core());    
        }
        
        Candoo_App_Resource::set('locale',$locale);
        
        
    }
    
    public function _initLangRoute()
    {
        $configs = $this->getConfig();
                
        $restLocale = $configs->locale->restricted;
        if (!empty($restLocale)) {
            // set restricted locale
            Candoo_App_Resource::get('locale')->setLocale($restLocale);

        	return ;
        }

        $request = Candoo_App_Resource::get('request');
        $pathInfo = $request->getPathInfo();
        
        /* Search for language in first stack of uri and reset baseurl against it
         * and set lang param
        * */
        // shift current lang from begining of uri
        $ptInf  = explode('/', ltrim($pathInfo,'/'));
        
        $locale = array_shift($ptInf);
        //           [fa]_IR vvvvvvvvvvvvvvvvv
        if (in_array($locale, explode(',',$configs->locale->languages) )) 
        {
        	// set locale from uri segment
            Candoo_App_Resource::get('locale')->setLocale($locale);
        		
        	$bs = $request->getBaseUrl();
        	$request->setBaseUrl($bs.'/'.$locale);
        	$request->setPathInfo('/'.rtrim(implode('/', $ptInf)));
        }
    }
    
    public function _initTranslateHelper()
    {
        /**
         * used in:
         * 	view translate helper
         * 	routing classes such as:
         * 		cPages_Lib_Route_Standard->getTranslator
         * 	
         */
        if (Candoo_Cache::isEnabled()) {
            cLocali_Lib_Translate::setCache(new Candoo_Cache_Core());
        }
        
       	Candoo_App_Resource::set('translator', new cLocali_Lib_Translate());
       	
       	/**
       	 * Plugin e translate ro ham register mikonim ke vazife daarad
       	 * pas az route va moshakhas shodan e module
       	 * file e language e aaan raa be class ta`rif konad
       	 */
       	Candoo_App::getInstance()->registerPlugin(new cLocali_Plugins_Translate());
    }
    
    public function _initDateTime()
    {
        $locale  = Candoo_App_Resource::get('locale')->toString();

        $configs = $this->getConfig();

        // set timezone to use inside Zend_Date and cLocali_Lib_Date ```````````````````````````````````````````|
        $timezone = $configs->datetime->{$locale}->timezone;
        if (!$timezone) {
            // timezone e zabaane pishfarz raa dar nazar migirad
            $def = $configs->locale->default;
            $timezone  = $configs->datetime->{$def}->timezone;
        }
        if ($timezone) {
            date_default_timezone_set($timezone);
        }
        // agar time zone set nashode bood tavasote date_default_timezone_get timezone server gerefte mishavad 
        
        
        // set default calendar system to use inside cLocali_Lib_Date ``````````````````````````````````````````|
        $calendar = $configs->datetime->{$locale}->calendar;
        if (!$calendar) {
        	// timezone e zabaane pishfarz raa dar nazar migirad
        	$def = $configs->locale->default;
        	$calendar  = $configs->datetime->{$def}->calendar;
        	if (!$calendar) {
        	    $calendar = 'gregorian';
        	}
        }
        cLocali_Lib_Date::setCalendar($calendar);
        
    }
    
}