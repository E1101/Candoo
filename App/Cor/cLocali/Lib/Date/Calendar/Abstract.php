<?php
class cLocali_Lib_Date_Calendar_Abstract
{
	protected $_timestamp = null;
	
	protected $_phpformat = null;
	
	protected $reservedWords = array(
			'a'=>'get_AmPm',
			'B'=>'get_Swatchtime',
			'd'=>'get_Day',
			'w'=>'get_Dayweek',
			'D'=>'get_Dayweekword',
			'l'=>'get_Dayweekwordlong',
			'j'=>'get_Daynotzero',
			't'=>'get_Dayofmonth',
			'z'=>'get_Dayofyear',
			'm'=>'get_Month',
			'M'=>'get_Monthword',
			'F'=>'get_Monthwordlong',
			'n'=>'get_Monthnotzero',
			'Y'=>'get_FullYear',
			'o'=>'get_Year8601',
			'y'=>'get_Year',
			'L'=>'get_YearBisestile',
			'H'=>'get_Hours',
			'g'=>'get_Hoursamnotzero',
			'h'=>'get_Hoursam',
			'G'=>'get_Hoursnotzero',
			'I'=>'get_Hourslegal',
			'O'=>'get_Hoursgreenwich',
			'i'=>'get_Minutes',
			's'=>'get_Seconds',
		);

	/**
	 * 
	 * @param string $format php format
	 * @param int|string $timestamp
	 */
	public function __construct($format,$timestamp = null) 
	{	    
		if (is_null($timestamp)) {
			$timestamp = time();
		} else {
			/** TODO: check that $date is valid */
			$date = strtotime($timestamp);
		}
		$this->_timestamp = $timestamp;
		
		$this->_phpformat = $format;
	}
	
	public function __toString()
	{
	    return $this->toString();
	}
		
	public function toString()
	{						
		$tokens = $this->_getTokens();
		$return = $this->_phpformat;
		
		// pas az har baar jaaigozini e kalamaat dar reshteie return
		// index be andaazeie toole kalameie jaaigozin shode afzaaiesh
		// miaabad
		$inPlus = 0;
		foreach ($tokens as $index => $t) {
		    $method = $this->reservedWords[$t];
			if (method_exists($this, $method)) {
		    	$value = $this->$method();
			} else {
			    $value = date($t,$this->_timestamp);
			}	
			
			$return =  $this->str_insert($value, $return, $index+$inPlus);
			$inPlus += strlen($value)-1;
			
		}
								
		return $this->_removeComments($return);
	}
	
	protected function str_insert($insertstring, $intostring, $offset) {
		$part1 = substr($intostring, 0, $offset);
		// you can disable +1 to have this index char to output
		$part2 = substr($intostring, $offset+1);
	
		$part1 = $part1 . $insertstring;
		$whole = $part1 . $part2;
		return $whole;
	}
	
	protected function _getTokens()
	{
	    $tokens = array();
	    $format = $this->_phpformat;
	    
	    if (strlen($format) <= 1 ) {
	        return array($format);
	    }
	           	    
	    for ($i = 0; isset($format[$i]); ++$i) {
	        if ($format[$i-1] != "\\") {
	            if ($format[$i] != "\\") {
	                $tokens[$i] = $format[$i];
	            }
	        }
	    }
	    
	    return $tokens;
	}
	
	protected function _removeComments($value)
	{
	    $return = '';
		if (strlen($value) <= 1 ) {
			return $value;
		}
		 
		for ($i = 0; isset($value[$i]); ++$i) {
			if ($value[$i] != "\\") {
				$return .= $value[$i];
			}
		}
		 
		return $return;
	}

	public function get_AmPm()
	{
		return date("a",$this->_timestamp);
	}
	
	public function get_Swatchtime()
	{
		return strtoupper(date("B",$this->_timestamp));
	}
	
	public function get_Day()
	{
		return date("d",$this->_timestamp);
	}
	
	public function get_Dayweek()
	{
		return date("w",$this->_timestamp);
	}
	
	public function get_Dayweekword()
	{	    
		return date("D",$this->_timestamp);
	}
	
	public function get_Dayweekwordlong()
	{
		return date("l",$this->_timestamp);
	}
	
	public function get_Daynotzero()
	{
		return date("j",$this->_timestamp);
	}
	
	public function get_Dayofmonth()
	{
		return date("t",$this->_timestamp);
	}
	
	public function get_Dayofyear()
	{
		return date("z",$this->_timestamp);
	}
	
	public function get_Month()
	{
		$month = date("m",$this->_timestamp);
		return (strlen($month)>1) ? $month : '0'.$month;
	}
	
	public function get_Monthword()
	{
		return date("M",$this->_timestamp);
	}
	
	public function get_Monthwordlong()
	{
		return date("F",$this->_timestamp);
	}
	
	public function get_Monthnotzero()
	{
		return date("n",$this->_timestamp);
	}
	
	public function get_FullYear()
	{
		return date("Y",$this->_timestamp);
	}
	
	public function get_Year()
	{
		return date("y",$this->_timestamp);
	}
	
	public function get_YearBisestile()
	{
		return date("L",$this->_timestamp);
	}
	
	public function get_Hours()
	{
		return date("H",$this->_timestamp);
	}
	
	public function get_Hoursamnotzero()
	{
		return date("g",$this->_timestamp);
	}
	
	public function get_Hoursam()
	{
		return date("h",$this->_timestamp);
	}
	
	public function get_Hoursnotzero()
	{
		return date("G",$this->_timestamp);
	}
	
	public function get_Hourslegal()
	{
		return date("I",$this->_timestamp);
	}
	
	public function get_Hoursgreenwich()
	{
		return date("O",$this->_timestamp);
	}
	
	public function get_Minutes()
	{
		return date("i",$this->_timestamp);
	}
	
	public function get_Seconds()
	{
		return date("s",$this->_timestamp);
	}

}
