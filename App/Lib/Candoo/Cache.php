<?php
final class Candoo_Cache
{
    protected static $_enabled;
    
    /**
     * Barresi mikonad ke aayaa mitavaanim az emkaanaate jQuery estefaade konim
     *  
     */
    public static function isEnabled()
    {
        return (self::$_enabled) ? true : false;
    }
    
    public static function setEnabled($bool)
    {
        self::$_enabled = ($bool) ? true : false;
    }
}