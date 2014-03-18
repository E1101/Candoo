<?php
class Hooks_BadWordsCensor_Hook extends Candoo_Hook 
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function filter($content) 
	{		
		$badWords  = $this->getParam('badWords');
		$separator = $this->getParam('seperator');
		
		if ($badWords == null || $badWords == '') 
		{
			return $content;
		}
		
		$badWords = explode($separator, $badWords);
		foreach ($badWords as $word) 
		{
			$newWord = '***';
			$content = str_replace(array(strtolower($word),strtoupper($word),ucfirst($word)), $newWord, $content);
		}
		
		return $content;
	}
}
