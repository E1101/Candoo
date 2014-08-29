<?php	
	/**
	 * ANCrypt - Data Security Class
	 *
	 * NORMAL CLASS
	 * Best suitable: Multiple instances
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
	 
	class Util_Datasecurity_Ancrypt implements Util_Datasecurity_Ancrypt_Interface
	{
		private $buffer;
		private $pass_key;
		private $pass_key_mod;
		
		/**
		 * Constuctor
		 * Create a key for every instance
		 *
		 * @param $pass_key - Key Encryption/Decryption key
		 */
		 
		public function __construct($pass_key)
		{
			$this->setKey($pass_key);
		}
		
		
		
		/**
		 * Set key for encryption and decryption
		 *
		 * @param $pass_key - Key that will be turnet into 160 hash code
		 */
		
		public function setKey($pass_key)
		{
			// Create Hash Key of $pass_key
			$md5 = sha1($pass_key);
			
			$this->pass_key = $md5;
			$this->pass_key_mod = strlen($this->pass_key);
		}
		
		
		
		/**
		 * Get key
		 *
		 * @return Passkey
		 */
		
		public function getKey()
		{
			return $this->pass_key;
		}
		
		
		
		/**
		 * Encrypt String
		 * @param str - String to encrypt
		 * @return StringBuffer - Encrypted String
		 */
		
		public function encrypt($str)
		{
			if( !$this->pass_key_mod )
			{
				die("Please set Pass-Key");
			}
			
			$this->buffer = "";
			
			
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
				$pk_pos = $i % $this->pass_key_mod;
				$pk = $this->pass_key[$pk_pos];
				
				// Frequency Attack Control bits
				$pk_fac = $this->pass_key_mod - $pk_pos - 1;
				
				// Encrypt character
				$c = $m;
				$c+= $pk;
				$c+= $pk_fac;
				
				// Add to the buffer
				$this->buffer .= chr($c);
			}
			
			return base64_encode($this->buffer);
		}
		
		
		
		/**
		 * Decrypt String
		 * @param str - Encrypted String
		 * @return StringBuffer - Decrypted String
		 */
		
		public function decrypt($str)
		{
			if( !$this->pass_key_mod )
			{
				die("Please set Pass-Key");
			}
			
			$this->buffer = "";
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
				$pk_pos = $i % $this->pass_key_mod;
				$pk = $this->pass_key[$pk_pos];
				
				// Frequency Attack Control bits
				$pk_fac = $this->pass_key_mod - $pk_pos - 1;
				
				// Encrypt character
				$m = $c;
				$c-= $pk;
				$c-= $pk_fac;
				
				// Add to the buffer
				$this->buffer .= chr($c);
			}
			
			
			return $this->buffer;
		}
		
		
		
		/**
		 * Encrypt file and copy encrypted content to a new file (if you want) otherwise it will return only the encrypted content
		 *
		 * @param $file_path - Path to file to be encrypted
		 * @param $new_file - Copy encrypted to a new file
		 * @return null (if second parameter is false, it will return String)
		 */
		 
		public function encryptFile($file_path, $new_file = false)
		{
			$str_buffer = array();
			
			// Check if file exists
			if( file_exists($file_path) )
			{
				$fp = fopen($file_path, "rb");
				
				while( !feof($fp) )
				{
					$content = fread($fp, filesize($file_path));
					$str_buffer[] = $this->encrypt($content);
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
		
		public function decryptFile($file_path, $new_file = false)
		{
			$str_buffer = array();
			
			// Check if file exists
			if( file_exists($file_path) )
			{
				$fp = fopen($file_path, "rb");
				
				while( !feof($fp) )
				{
					$content = fread($fp, filesize($file_path));
					$str_buffer[] = $this->decrypt($content);
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