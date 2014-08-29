<?php
class Candoo_Assets
{	
    /**
     * Url e dastresi be assets haa ast
     * 
     * @param string $section | mitavaanad js, css, yaa file haaye yek module baashad
     */
    public static function getURL($section)
    {
        /**
         * @todo test konad ke folder e in section mojood baashad
         */
        return Candoo_Uri::build(array(
        		Candoo_App::getBasePath(),
        		APP_FRONTEND,
        		self::getFolderName(),
        		$section
        ));
    }
    
    public static function getFolderName()
    {
        return end(explode(DS,APP_DIR_ASSETS));
    }
}
