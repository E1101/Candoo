<?php


	/**
	 * @interface ANCryptStaticInterface
	 * @author Arlind Nushi
	 */
	
	interface Util_Datasecurity_Ancryptst_Interface
	{
		public static function setKey($pass_key);
		
		public static function encrypt($str);
		
		public static function decrypt($str);
		
		public static function encryptFile($file_path, $new_file = false);
		
		public static function decryptFile($file_path, $new_file = false);
	}
?>