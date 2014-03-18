<?php
class Localization_Lib_Date_Gregorian
{
	//These save some time
	const ISO_TIME_FORMAT_STRING = 'Y/m/d H:i:s';
	const MYSQL_TIMESTAMP_TIME_FORMAT_STRING = 'YmdHis';
	const USA_TIME_FORMAT_STRING = 'm/d/Y H:i:s';
	const PHP_TIME_FORMAT_STRING = 'U';
	
	protected $_timestamp = null;

	
	public function __construct($date = null) 
	{
		if (is_null($date)) {
			$date = time();
		} else {
			/** TODO: check that $date is valid */
			$date = strtotime($date);
		}

		$this->_timestamp = $date;
	}
		
	public function date($formatString)
	{
		$reservedWords = array(
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
		
		$value = '';
		
		$tokens = $this->_toToken($formatString);
		
		Zend_Debug::dump($tokens);
		
		foreach ($tokens as $t) {
		    if (array_key_exists($t, $reservedWords)){
		        if (method_exists($this, $reservedWords[$t])) {
		            
		            $value .= $this->$reservedWords[$t]();
		        }
		    } else {
		        $value .= $t;
		    }
		}
				
		return $value; 
	}
	
	/**
	 * Internal method to apply tokens
	 *
	 * @param string $part
	 * @param string $locale
	 * @return string
	 */
	private function _toToken($part) {
		// get format tokens
		$return = array();
		$comment = false;
		$format  = '';
		$orig    = '';
		for ($i = 0; isset($part[$i]); ++$i) {
			if ($part[$i] == "'") {
				$comment = $comment ? false : true;
				if (isset($part[$i+1]) && ($part[$i+1] == "'")) {
					$comment = $comment ? false : true;
					$format .= "\\'";
					++$i;
				}
	
				$orig = '';
				continue;
			}
	
			if ($comment) {
				$format .= '\\' . $part[$i];
				$orig = '';
			} else {
				$orig .= $part[$i];
				if (!isset($part[$i+1]) || (isset($orig[0]) && ($orig[0] != $part[$i+1]))) {
					/* $format .= self::_parseIsoToDate($orig); */
					$return[] = $orig;
					$orig  = '';
				}
			}
		}
	
		return $return;
	}
	
	/**
	 * bar asaase _timestamp age raa nesbat be zamaane fe`li bar migardaanad
	 * va dar ghaalebe format baa jaaigozinie maghadir aan raa be khorooji ersaal mikonad
	 * 
	 * TODO : Felan bozorgtarin zamaane ghaabele bargasht rooz ast
	 */
	public function getAge($format = '%d day %h hour %m minutes %s second')
	{
		$currentTime = time();
		$diffTime = time() - $this->_timestamp;
		
		// get Second
		$retSec = $diffTime % 60;
		
		// get Minutes
		$minutes = $diffTime / 60;
		$retMin = $minutes % 60;
		 
		// get Hours
		$hours =  $minutes / 60;
		$retHour = $hours % 24;
		
		$days = $hours / 24;	
		$days = floor($days);
		
		/*
		 * az $format dar soorati ke motaghaier haaie rooz ya hour yaa minute ,...
		 * mojood nabood hazf mishavad
		 * 
		 */
		if ($days == 0){
			$s = strpos($format, '%d');
			$e = strpos($format, '%',$s+1);
			if (!$e) {$e = strlen($format)-$s;}
			
			$rmStr = substr($format,$s,$e);
			$format = str_replace($rmStr, '', $format);
		}

		if ($retHour == 0){
			$s = strpos($format, '%h');
			$e = strpos($format, '%',$s+1);
			if (!$e) {$e = strlen($format)-$s;}
			
			$rmStr = substr($format,$s,$e);
			$format = str_replace($rmStr, '', $format);
		}

		if ($retMin == 0){
			$s = strpos($format, '%m');
			$e = strpos($format, '%',$s+1);
			if (!$e) {$e = strlen($format)-$s;}
			
			$rmStr = substr($format,$s,$e);
			$format = str_replace($rmStr, '', $format);
		}

		if ($retSec == 0){
			$s = strpos($format, '%s');
			$e = strpos($format, '%',$s+1);
			if (!$e) {$e = strlen($format)-$s;}
			
			$rmStr = substr($format,$s,$e);
			$format = str_replace($rmStr, '', $format);
		}
		
		/*
		 * 
		 * Inser values in $format places
		 * 
		 */
		
		$format = str_replace('%d', $days, $format);
		$format = str_replace('%h', $retHour, $format);
		$format = str_replace('%m', $retMin, $format);
		$format = str_replace('%s', $retSec, $format);
		
		return $format;
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

	public function parse() 
	{
		$result['second'] = $this->get_Seconds();
		$result['minute'] = $this->get_Minutes();
		$result['hour']   = $this->get_Hours();
		$result['month']  = $this->get_Month();
		$result['day']    = $this->get_Day();
		$result['year']   = $this->get_Year();
		
		return $result;
	}
	
}
