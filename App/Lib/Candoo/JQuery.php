<?php
final class Candoo_JQuery
{
    /**
     * Barresi mikonad ke aayaa mitavaanim az emkaanaate jQuery estefaade konim
     *  
     */
    public static function isEnabledView()
    {
        $view = Candoo_App_Resource::get('view');
        
        return (false !== $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'));
    }
    
    public static function enableNoConflictMode()
    {
        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
    }
    
    public static function disableNoConflictMode()
    {
        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
    }
}