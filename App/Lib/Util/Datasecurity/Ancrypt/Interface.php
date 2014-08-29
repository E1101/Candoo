<?php


	/**
	 * @interface ANCryptInterface
	 * @author Arlind Nushi
	 */
	
	interface Util_Datasecurity_Ancrypt_Interface
	{
		public function setKey($pass_key);
		
		public function getKey();
		
		public function encrypt($str);
		
		public function decrypt($str);
		
		public function encryptFile($file_path, $new_file = false);
		
		public function decryptFile($file_path, $new_file = false);
	}
?>