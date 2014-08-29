<?php
class cLocali_Lib_Date_Calendar_Persian extends cLocali_Lib_Date_Calendar_Abstract
{    
    protected $d;  	  // Dayweek
	protected $jd; 	  // Day
	protected $jm; 	  // Month
	protected $jy; 	  // FullYear
	protected $jy_s;  // Year
	
	// te`daade rooz haa dar har maah
	protected $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
        
    private function div($a,$b) {
	  return (int) ($a / $b);
	}
	
	public function __construct($format,$timestamp=null)
	{
	    parent::__construct($format,$timestamp);
	    $this->calculateCalendar();
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
    
	public function get_Day()
	{
		return $this->jd;
	}
	
	public function get_Dayweek()
	{
		return $this->d;
	}
	
	
	public function get_Daynotzero()
	{
		return $this->jd;
	}
	
	public function get_Dayofmonth()
	{
		return $this->j_days_in_month[$this->get_Month()];
	}
	
	/*public function get_Dayofyear()
	{
		return date("z",$this->_timestamp);
	}*/

	public function get_Month()
	{
		return $this->jm;
	}
	
	public function get_Monthnotzero()
	{
		return $this->jm;
	}
	
	public function get_FullYear()
	{
		return $this->jy;
	}
	
	public function get_Year8601()
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
		
	
}
