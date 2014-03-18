<?php
class Localization_Lib_Date_Persian extends Flex_Date
{
	protected $week   = Array("&#1610;&#1603;&#1588;&#1606;&#1576;&#1607;","&#1583;&#1608;&#1588;&#1606;&#1576;&#1607;","&#1587;&#1607; &#1588;&#1606;&#1576;&#1607;","&#1670;&#1607;&#1575;&#1585;&#1588;&#1606;&#1576;&#1607;","&#1662;&#1606;&#1580;&#8204;&#1588;&#1606;&#1576;&#1607;","&#1580;&#1605;&#1593;&#1607;","&#1588;&#1606;&#1576;&#1607;");
    protected $months = Array("&#1601;&#1585;&#1608;&#1585;&#1583;&#1610;&#1606;","&#1575;&#1585;&#1583;&#1610;&#1576;&#1607;&#1588;&#1578;","&#1582;&#1585;&#1583;&#1575;&#1583;","&#1578;&#1610;&#1585;","&#1605;&#1585;&#1583;&#1575;&#1583;","&#1588;&#1607;&#1585;&#1610;&#1608;&#1585;","&#1605;&#1607;&#1585;","&#1570;&#1576;&#1575;&#1606;","&#1570;&#1584;&#1585;","&#1583;&#1610;","&#1576;&#1607;&#1605;&#1606;","&#1575;&#1587;&#1601;&#1606;&#1583;");
    
    protected $d;  	  // Dayweek
	protected $jd; 	  // Day
	protected $jm; 	  // Month
	protected $jy; 	  // FullYear
	protected $jy_s;  // Year
    
    protected function init()
    {
    	$this->calculateCalendar();
    }
    
    private function div($a,$b) {
	  return (int) ($a / $b);
	}
     
    protected function calculateCalendar()
    {
    	/*
         * Get Gregorian times
         */
		 $g_y = parent::get_FullYear();
		 $g_m = parent::get_Month();
		 $g_d = parent::get_Day();
		 $d   = parent::get_Dayweek();
		   
		 $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		 $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
			
		 $gy = $g_y-1600;
		 $gm = $g_m-1;
		 $gd = $g_d-1;
			
		 $g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);
		 for ($i=0; $i < $gm; ++$i) {
		 	$g_day_no += $g_days_in_month[$i];
		 }
			
		 if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) {
			/* leap and after Feb */
			$g_day_no++;
		 }
		 $g_day_no += $gd;
			
		 $j_day_no = $g_day_no-79;
		 $j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
		 $j_day_no = $j_day_no % 12053;
		 $jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
		 $j_day_no %= 1461;
		 if ($j_day_no >= 366) {
		 	$jy += $this->div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		 }
		 for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i){
			$j_day_no -= $j_days_in_month[$i];
		 }
		 $jm = $i+1;
		 $jd = $j_day_no+1;
		 $jy_s = mb_substr($jy, 2, 2);
		 if ($jd<10) {
			$jd = "0".$jd;
		 }
		 
		 $this->d  = $d;	  // Dayweek
		 $this->jd = $jd;	  // Day
		 $this->jm = $jm;	  // Month
		 $this->jy = $jy;	  // FullYear
		 $this->jy_s = $jy_s; // Year	 
    }
    
	public function get_AmPm()
	{
		$ampm = (strtolower(date("a",$this->_timestamp))=='am') ? 'ق ظ' : 'ب ظ';
		return $ampm;
	}
	
	public function get_Day()
	{
		return $this->jd;
	}
	
	public function get_Dayweek()
	{
		return $this->d;
	}
	
	public function get_Dayweekword()
	{
		return $this->week[$this->d];
	}
	
	public function get_Dayweekwordlong()
	{
		return $this->week[$this->d];
	}
	
	public function get_Daynotzero()
	{
		return $this->jd;
	}
	
	/*public function get_Dayofmonth()
	{
		return date("t",$this->_timestamp);
	}*/
	
	/*public function get_Dayofyear()
	{
		return date("z",$this->_timestamp);
	}*/

	public function get_Month()
	{
		return $this->jm;
	}
	
	public function get_Monthword()
	{
		return $this->months[$this->jm-1];
	}
	
	public function get_Monthwordlong()
	{
		return $this->months[$this->jm-1];
	}
	
	public function get_Monthnotzero()
	{
		return $this->jm;
	}
	
	public function get_FullYear()
	{
		return $this->jy;
	}

	public function get_Year()
	{
		return $this->jy_s;
	}
	
	/*public function get_YearBisestile()
	{
		return date("L",$this->_timestamp);
	}*/
	
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
