<?php	
	/**
	 * ANCrypt - Data Security Class
	 *
	 * CLASS WITH STATIC METHODS
	 * Best suitable: For Content Management Systems
	 * In static class you can set pass key once and use it everywhere without need to add everytime new key
	 *
	 * Encryption class created by
	 * ARLIND NUSHI
	 *
	 * Encryption strength:
	 * 160 bit
	 *
	 * Algorithm founder:
	 * ARLIND NUSHI
	 *
	 * Encryption Type:
	 * Symmetric Encryption (one-key-encrypt-decrypt)
	 *
	 * Email:
	 * arlind.nushi@gmail.com
	 */
	 
	class Util_Datasecurity_Ancryptst implements Util_Datasecurity_Ancryptst_Interface
	{
		private static $buffer;
		private static $pass_key;
		private static $pass_key_mod;
		
		public static function setKey($pass_key)
		{
			// Create Hash Key of $pass_key
			$md5 = sha1($pass_key);
			
			self::$pass_key = $md5;
			self::$pass_key_mod = strlen(self::$pass_key);
		}
		
		
		/**
		 * Encrypt String
		 * @param str - String to encrypt
		 * @return StringBuffer - Encrypted String
		 */
		
		public static function encrypt($str)
		{
			if( !self::$pass_key_mod )
			{
				die("Please set Pass-Key");
			}
			
			self::$buffer = "";
			
			
			// M-message character, PK-pass key character
			$m = "";
			$pk = "";
			
			// PK-Pass key position, PK-Frequency Attack Control
			$pk_pos;
			$pk_fac;
			
			// Encrypted Character
			$c;
			
			$str_len = strlen($str);
			
			for( $i=0; $i<$str_len; $i++ )
			{
				// Get M
				$m = ord($str[$i]);
				
				// Get Pass Key character at position i % pass_key_mod
				$pk_pos = $i % self::$pass_key_mod;
				$pk = self::$pass_key[$pk_pos];
				
				// Frequency Attack Control bits
				$pk_fac = self::$pass_key_mod - $pk_pos - 1;
				
				// Encrypt character
				$c = $m;
				$c+= $pk;
				$c+= $pk_fac;
				
				// Add to the buffer
				self::$buffer .= chr($c);
			}
			
			return base64_encode(self::$buffer);
		}
		
		
		
		/**
		 * Decrypt String
		 * @param str - Encrypted String
		 * @return StringBuffer - Decrypted String
		 */
		
		public static function decrypt($str)
		{
			if( !self::$pass_key_mod )
			{
				die("Please set Pass-Key");
			}
			
			self::$buffer = "";
			$str = base64_decode($str);
			
			// C-crypted character, PK-pass key character
			$c = "";
			$pk = "";
			
			// PK-Pass key position, PK-Frequency Attack Control
			$pk_pos;
			$pk_fac;
			
			// Decrypted Message Character
			$m;
			
			$str_len = strlen($str);
			
			for( $i=0; $i<$str_len; $i++ )
			{
				// Get C
				$c = ord($str[$i]);
				
				// Get Pass Key character at position i % pass_key_mod
				$pk_pos = $i % self::$pass_key_mod;
				$pk = self::$pass_key[$pk_pos];
				
				// Frequency Attack Control bits
				$pk_fac = self::$pass_key_mod - $pk_pos - 1;
				
				// Encrypt character
				$m = $c;
				$c-= $pk;
				$c-= $pk_fac;
				
				// Add to the buffer
				self::$buffer .= chr($c);
			}
			
			return self::$buffer;
		}
		
		
		
		/**
		 * Encrypt file and copy encrypted content to a new file (if you want) otherwise it will return only the encrypted content
		 *
		 * @param $file_path - Path to file to be encrypted
		 * @param $new_file - Copy encrypted to a new file
		 * @return null (if second parameter is false, it will return String)
		 */
		 
		public static function encryptFile($file_path, $new_file = false)
		{
			$str_buffer = "";
			
			// Check if file exists
			if( file_exists($file_path) )
			{
				$fp = fopen($file_path, "rb");
				
				while( !feof($fp) )
				{
					$content = fread($fp, 1024);
					$str_buffer[] = self::encrypt($content);
				}
				
				fclose($fp);
				
				if( $new_file )
				{
					if( @$fp = fopen($new_file, "w+") )
					{
						foreach($str_buffer as $str_block)
						{
							fwrite($fp, $str_block);
						}
						
						fclose($fp);
						
						return true;
					}
					else
						return false;
				}
				else
					return implode("", $str_buffer);
			}
			
			return null;
		}
		
		
		/**
		 * Decrypt file and copy decrypted content to a new file (if you want) otherwise it will return only the decrypted content
		 *
		 * @param $file_path - Path to file to be decrypted
		 * @param $new_file - Copy decrypted to a new file
		 * @return null (if second parameter is false, it will return String)
		 */
		
		public static function decryptFile($file_path, $new_file = false)
		{
			$str_buffer = "";
			
			// Check if file exists
			if( file_exists($file_path) )
			{
				$fp = fopen($file_path, "rb");
				
				while( !feof($fp) )
				{
					$content = fread($fp, 1024);
					$str_buffer[] = self::decrypt($content);
				}
				
				fclose($fp);
				
				if( $new_file )
				{
					if( @$fp = fopen($new_file, "w+") )
					{
						foreach($str_buffer as $str_block)
						{
							fwrite($fp, $str_block);
						}
						
						fclose($fp);
						
						return true;
					}
					else
						return false;
				}
				else
					return implode("", $str_buffer);
			}
			
			return null;
		}
	}
?>