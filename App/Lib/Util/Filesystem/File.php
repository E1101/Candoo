<?php
class Util_Filesystem_File
{
	private static $filtered_name = array('.svn');

	/**
	 * Zir Shaakhe haaie yek directory raa list mikonad 
	 * be gheir az naam haai`e ke dar "$this->filtered" hastand va "." , ".."  
	 *  
	 * @param string $dir Path to the directory
	 * @param string $ext_filtered .extension directory ke nabaaiad list shavad without "."
	 * @return array
	 */
	public static function getSubDir($dir,$ext_filtered=null) 
	{
		if (!file_exists($dir)) {
			return array();
		}
		
		$subDirs 	 = array();

		$dirIterator = new DirectoryIterator($dir);
		
		// get only specific file extension 
		//$dirIterator = new GlobIterator($dir.DS.'*.xml');
		
		foreach ($dirIterator as $dir)
		{
			if ($dir ->isDot() || !$dir ->isDir()) { continue; }
			
			$dir = $dir ->getFilename();
			// agar directory baa in naam nabaaida namaaiesh mishod
			if (in_array($dir,self::$filtered_name)) { continue; }
			// agar directory baa yek pasvand e khaas nabaaiad list mishod
			if ($ext_filtered) {
				$dirExt = pathinfo($dir,PATHINFO_EXTENSION);
				if($dirExt == $ext_filtered) { continue; }
			}
			
			$subDirs[] = $dir;
		}
				
		return $subDirs;	
	}
	
	/**
	 * Az Yek masir shoroo mikonad va tamaami e file haaye mojood dar
	 * in saakhtaar raa mikhaanad va function raa bar rooie aan ejraa
	 * mikonad va khoroojie in function raa dar array negah midaarad
	 * 
	 * @param unknown_type $dir | path to follow
	 * @param unknown_type $callback | function
	 * @param unknown_type $fext | extension of files, can be array
	 * @throws Exception
	 */
	public static function crawlDirectory($dir,$callback,$fext=null,$depth=null) 
	{
		if (!is_dir($dir)){
			throw new Exception('Directory not found :'.$dir);
		}
		if ($callback){
			if (!function_exists($callback)){
				throw new Exception('Function not callable :'.$callback);
			}
		}
		$return = array();
		
		$files = self::getFiles($dir,$fext);
		if ($files){
			foreach ($files as $f){
				$return[] = call_user_func($callback,$dir.DS.$f);
			}
		}
		if ($depth > 1 || $depth === null) {
			$subDirs = self::getSubDir($dir);
			if ( !empty($subDirs) ){
				foreach ($subDirs as $d){
					$depth = ($depth === null) ? $depth : $depth-1;
					$return = array_merge(self::crawlDirectory($dir.DS.$d,$callback,$fext,$depth),$return);
				}
			}
		}
		
		return $return;
	}
	
	public static function getFiles($dir,$extension=null)
	{
		if (!file_exists($dir) || !is_dir($dir)) {
			return array();
		}
		if (is_string($extension) && $extension != null) {
			$extension = array($extension);
		} elseif (!is_array($extension) && $extension != null) {
			throw new Exception('Invalid extension format');
		}
		
		$files = array();
		
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file)	{
			if ($file ->isDir()) { continue ;}
			
			if ($extension)	{
				if ( in_array(pathinfo($file,PATHINFO_EXTENSION), $extension) ) {
					$files[] = $file->getFilename();
				}
			} else {
				$files[] = $file->getFilename();;
			}
		}
		
		return $files;
	}
	
	/**
	 * @param string $dir
	 * @return boolean
	 */
	public static function deleteRescursiveDir($dir) 
	{
		if (is_dir($dir)) {	        	
            $dir 	 = (substr($dir, -1) != DS) ? $dir.DS : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir.$file)) {
                        @unlink($dir.$file);
                    } else {
                        self::deleteRescursiveDir($dir.$file);
                    }
                }
            }
            closedir($openDir);
            @rmdir($dir);
        }
        
        return true;
	}
	
	/**
	 * @param string $source
	 * @param string $dest
	 * @return boolean
	 */
	public static function copyRescursiveDir($source, $dest) 
	{
		$openDir = opendir($source);
		if (!file_exists($dest)) {
	    	@mkdir($dest);
		}
		while ($file = readdir($openDir)) {
			if (!in_array($file, array(".", ".."))) {
	        	if (is_dir($source . DS . $file)) { 
	                self::copyRescursiveDir($source . DS . $file, $dest . DS . $file); 
	            } else { 
	                copy($source . DS . $file, $dest . DS . $file); 
				} 
	        }
	    }
	    closedir($openDir);
		
		return true;
	}
	
	/**
	 * Create sub-directories of given directory
	 * 
	 * @param string $root Path to root directory
	 * @param string $path Relative path to new created directory in format a/b/c (on Linux) 
	 * or a\b\c (on Windows)
	 */
	public static function createDirs($root, $path) 
	{
		$root 	 = rtrim($root, DS);
		$subDirs = explode(DS, $path);
		if ($subDirs == null) {
			return;
		}
		$currDir = $root;
		foreach ($subDirs as $dir) {
			$currDir = $currDir . DS . $dir;
			if (!file_exists($currDir)) {
				mkdir($currDir);
			}
		}	 
	}
	
  	public static function fwriteUTF8($filename,$content) 
  	{
      	$f = fopen($filename,"wb");
      	# Now UTF-8 - Add byte order mark
      	fwrite($f, pack("CCC",0xef,0xbb,0xbf));
      	fwrite($f,$content);
      	fclose($f);
  	}
	
	
    public static function getNewFilenameInDirectory ($pathDirectory,$actualName=null) 
  	{
  		$filePathName = ($actualName) 
  		          ? $pathDirectory.$actualName
  		          : $pathDirectory ;

  		$parsedFileName = pathinfo($filePathName);
  		$fileName = $parsedFileName['basename'];
  		
  		                          $i = 0 ;
  		while (file_exists($filePathName))
  		{
  			$i ++ ;
  			$fileName = $parsedFileName['filename'].'('.$i.')'.'.'.$parsedFileName['extension'];
  			$filePathName = $parsedFileName['dirname'].DS.$fileName;
  		}
  	
  		return $fileName;
	}
	
	/**
	 * فایل و پسوند آن را بر میگرداند MIME Type مسیر و نام فایل را میگیرد و   
	 * 
	 * @param $pathFileName
	 * @return array | extension و mime آرایه ای شامل دو کلید 
	 */
	public function getFileMimeType($pathFileName)
	{
		if (file_exists($pathFileName))
		{
			$ext = pathinfo($pathFileName,PATHINFO_EXTENSION);
			
			if (function_exists('finfo_open_')) {				
				$finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
				$mime  = finfo_file($finfo, $pathFileName);
				$mime  = substr($mime,0,strpos($mime,';'));
				finfo_close($finfo);
			} else {
				if (!function_exists('mime_content_type')) {
                    function mime_content_type($f) { 
                       $file = escapeshellarg( $f );
                       $mime = shell_exec("file -bi " . $file); 
                    } 
                }
                	$mime = mime_content_type($pathFileName);
			}
			
			if ($mime == null && function_exists('getimagesize') ) {
				$mime = getimagesize($pathFileName);
				$mime = $mime['mime'];
			}
			
			$outPut = new stdClass();
			
			$outPut ->mime     = $mime ;
			$outPut ->extension = $ext  ;

			return $outPut;
		}
		else 
		{
			return false;
		}
	}
	
  	public static function getFileSize($file) 
  	{
       	$bytes = array("B", "KB", "MB", "GB", "TB", "PB"); 
    
       	// replace (possible) double slashes with a single one
       	$file = str_replace("//", "/", $file); 
       	$size = filesize($file);
    
       	$i = 0;
       	while ($size >= 1024) 
       	{ //divide the filesize (in bytes) with 1024 to get "bigger" bytes 
           	$size = $size/1024;
           	$i++; 
       	}
    
       	if ($i > 1) 
       	{ 
           	// you can change this number if you like (for more precision)
           	return round($size,1)."&nbsp;".$bytes[$i]; 
       	} 
       	else 
       	{ 
           return round($size,0)."&nbsp;".$bytes[$i]; 
       	}
  	}
       
}// end of class
