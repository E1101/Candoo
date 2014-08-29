<?php


	/**
	 * @interface SafeCookieInterface
	 * @author Arlind Nushi
	 */
	
	interface Util_Datasecurity_Safecookie_Interface
	{
		public static function setSuperKey($pass_key);
		
		public static function set($cookie_details, $pass_key = null);
		
		public static function get($cookie_name, $pass_key = null);
		
		public static function validate($cookie_name, $pass_key = null);
		
		
		public function setCookie($cookie_name, $cookie_value, $seconds_alive = 1800, $path = '/');
		
		public function getCookie($cookie_name);
		
		public function validateCookie($cookie_name);
		
		public function getOriginal($cookie_name);
	}
?>