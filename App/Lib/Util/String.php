<?php
class Util_String
{
    public static function makeRandStr ($length = 8, $useupper = true, $usespecial = true, $usenumbers = true)
    {
    	$charset = "abcdefghijklmnopqrstuvwxyz";
    	if ($useupper)   $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	if ($usenumbers) $charset .= "123456789"; //no zero because it looks like uppercase o
    	if ($usespecial) $charset .= "_-~!@#$%^*()+={}|]?[:;,."; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
    	
    	for ($i=0; $i<$length; $i++) {
    	     $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
    	}
    	
    	return $key;
    }
    
    
    public static function makeSafe($string)
    {
    	$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\-]#', '#^\.#');
    	return preg_replace($regex, '', $string);
    }
}
