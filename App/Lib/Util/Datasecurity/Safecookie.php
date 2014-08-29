<?php	
	/**
	 * SafeCookie - Secure cookies encrypted via ANCrypt
	 *
	 * Encryption Key Strength:
	 * 160 bit
	 *
	 * Key type:
	 * Read/Write
	 *
	 * Encryption Type:
	 * Symmetric Encryption (one-key-encrypt-decrypt)
	 *
	 * Author:
	 * Arlind Nushi
	 *
	 * Email:
	 * arlind.nushi@gmail.com
	 */
	
	class Util_Datasecurity_Safecookie implements Util_Datasecurity_Safecookie_Interface
	{
		private $pass_key;
		
		private static $super_key;
		
		/**
		 * Constructor
		 * Set key that will be used to encrypt and decrypt cookies
		 *
		 * @param $pass_key - Encryption/Decryption key
		 */
		 
		public function __construct($pass_key)
		{
			if( strlen($pass_key) > 0 )
			{
				$this->pass_key = $pass_key;
			}
			else
				die("SafeCookie::error:: Please enter Passkey for SafeCookie");
		}
		
		
		/**
		 * Set super key
		 * For static context
		 *
		 * @return null
		 */
		
		public static function setSuperKey($pass_key)
		{
			self::$super_key = $pass_key;
		}
		
		
		
		/**
		 * Set cookie
		 *
		 * @param $cookie_name - Cookie name (hash value)
		 * @param $cookie_value - Value to cookie (encrypted)
		 * @param $seconds_alive - Cookie alive, default 30 minutes
		 * @param $path - Cookie path, default /
		 */
		  
		public function setCookie($cookie_name, $cookie_value, $seconds_alive = 1800, $path = '/')
		{
			// Check if alive-time is valid number
			if( !is_numeric($seconds_alive) || $seconds_alive < 0 )
				$seconds_alive = 1800;
			
			// Create Hash for cookie name
			$name_hash = md5($cookie_name);
			
			// Cookie Validator Hash Value - Integrity Check
			$cookie_validator = md5($cookie_value);
			
			// Concatenate Cookie values (this is needed to validate cookies)
			$cookie_value_valid = $cookie_validator . $cookie_value;
						
			// Encrypt Cookie Value
			$ancrypt = new Util_Datasecurity_Ancrypt($this->pass_key);
			
			$encrypted_value = $ancrypt->encrypt($cookie_value_valid);
					
			// Set Cookie
			setcookie($name_hash, $encrypted_value, time()+$seconds_alive, $path);
		}
		
		
		/**
		 * Get Cookie
		 * 
		 * @param $cookie_name - Cookie to get
		 * @return String - if cookie exits, otherwise Null
		 */
		 
		public function getCookie($cookie_name)
		{			
			// Generate Cookie Name hash
			$name_hash = md5($cookie_name);
			
			// Get Cookie Value
			$cookie_value = $_COOKIE[$name_hash];
			
			// Null if cookie doesn't exists
			if( empty($cookie_value) )
				return null;
			
			// Decrypt Cookie Value
			$ancrypt = new Util_Datasecurity_Ancrypt($this->pass_key);
			$decrypted_value = $ancrypt->decrypt($cookie_value);
			
			$cookie_value = substr($decrypted_value, 32);
			
			return $cookie_value;
		}
		
		
		/**
		 * Validate Cookie
		 * Check if contents of cookie has changed
		 * 
		 * @return boolean
		 */
		
		public function validateCookie($cookie_name)
		{			
			// Generate Cookie Name hash
			$name_hash = md5($cookie_name);
			
			// Get Cookie Value
			$cookie_value = $_COOKIE[$name_hash];
			
			// False if cookie doesn't exists
			if( empty($cookie_value) )
				return false;
			
			// Decrypt Cookie Value
			$ancrypt = new Util_Datasecurity_Ancrypt($this->pass_key);
			$decrypted_value = $ancrypt->decrypt($cookie_value);
			
			// Get Validation Value
			$hash_value = substr($decrypted_value, 0, 32);
			$current_value = substr($decrypted_value, 32);
			
			// Generate hash of current cookie value
			$hash_current_value = md5($current_value);
			
			// Compare both hash values
			if( $hash_current_value == $hash_value )
			{
				return true; // Data has been not altered
			}
			
			return false;
		}
		
		
		/**
		 * Set cookie from static declaration
		 */
		 
		public static function set($cookie_details, $pass_key = null)
		{
			if( !$pass_key )
			{
				$pass_key = self::$super_key;
			}
			
			if( is_array($cookie_details) && count($cookie_details) >= 2 && !empty($pass_key) )
			{
				$safe_cookie = new Util_Datasecurity_Ancrypt_Safecookie($pass_key);
				$safe_cookie->setCookie($cookie_details[0], $cookie_details[1], $cookie_details[2], $cookie_details[3]);
			}
			else
			{
				echo "SafeCookie::set::error: set(param1, param2) method expects two parameters: <br />1) param1 : Array(cookie_name, cookie_value[, seconds_alive, path])<br />2) param2 : string Passkey";
			}
		}
		
		
		/**
		 * Get cookie from static declaration
		 */
		 
		public static function get($cookie_name, $pass_key = null)
		{
			if( !$pass_key )
			{
				$pass_key = self::$super_key;
			}
			
			if( !empty($cookie_name) && !empty($pass_key) )
			{
				$safe_cookie = new Util_Datasecurity_Ancrypt_Safecookie($pass_key);
				return $safe_cookie->getCookie($cookie_name);
			}
			else
			{
				echo "SafeCookie::get::error: Please insert cookie name and passkey";
				return null;
			}
		}
	
	
		/**
		 * Validate cookie from static declaration
		 */
		 
		public static function validate($cookie_name, $pass_key = null)
		{
			if( !$pass_key )
			{
				$pass_key = self::$super_key;
			}
			
			if( !empty($cookie_name) && !empty($pass_key) )
			{
				$safe_cookie = new Util_Datasecurity_Ancrypt_Safecookie($pass_key);
				return $safe_cookie->validateCookie($cookie_name);
			}
			else
			{
				echo "SafeCookie::get::error: Please insert cookie name and passkey";
				return false;
			}
		}
		
		
		/**
		 * Get original cookie content (encrypted)
		 *
		 * @return Cookie Name , Cookie Value
		 */
		
		public function getOriginal($cookie_name)
		{
			$cookie_name  = md5($cookie_name);
			$cookie_value = $_COOKIE[ $cookie_name ];
			
			return "Cookie(name: $cookie_name, value: $cookie_value);";
		}
	}
?>