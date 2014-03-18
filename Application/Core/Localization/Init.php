<?php
class Localization_Init extends Candoo_App_Initalize_Abstract
{
    public function _initInital()
    {
        // baraaie inke avalin stack az url baayad dar ekhtiaare subsite baashad
        $this->init('Multisite');
                
        $configs = $this->getConfig();
        
        // register locale based on browser,server detection
        try 
        {
            $locale = new Zend_Locale('auto');
            
            $lc = $locale->toString();

            //            [fa]_IR vvvvvvvvvvvvvvvvv
            if (! in_array(current(explode('_', $lc)), explode(',', $configs->locale->languages))  ) {
                $lc = (!empty($configs->locale->restricted)) 
                	? $configs->locale->restricted
                	: $configs->locale->default;
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
        
        // if no locale can be detected in any Zend_Locale based class, automatically the locale de will be used
        // exp. $date = new Zend_Date();
        /* Candoo_App_Resource::set('locale' ... az yek setter estefaade mikonad ke locale ro dar Zend_Registery
         * baa kelid e Zend_Locale sabt mikonad
         * */
        //$lc = 'fa_IR';
        $locale->setLocale($lc);       
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
        if (in_array(current(explode('_',$locale)), explode(',',$configs->locale->languages) )) 
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
         * 		Pages_Lib_Route_Standard->getTranslator
         * 	
         */
       	Candoo_App_Resource::set('translator', new Localization_Lib_Translate());
    }
    
    public function _initDateTime()
    {
        $configs = $this->getConfig();
        
        /* Zend_Locale_Format::setOptions(array(
        	'date_format' => 'dd.MMMM.YYYY')
        ); */
        
        date_default_timezone_set('Asia/Tehran');
        $date = new Localization_Lib_Date();
        echo $date->getDate('y');
        
        echo new Zend_Date(Candoo_App_Resource::get('locale'));
        exit;
        
       
        
    }
    
}