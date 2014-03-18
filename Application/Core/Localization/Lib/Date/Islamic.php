<?php
class Localization_Lib_Date_Islamic extends Flex_Date
{
	protected $week   = Array(
							'أح',
							'أث',
							'ثل',
							'أر',
							'خم',
							'جم',
							'سب'
	);
	protected $weeklong   = Array(
							'الأحَد',
							'الإثْنَيْن',
							'الثُّلاثَاء',
							'الأرْبِعَاء',
							'الْخَمِيس',
							'الْجُمْعَة',
							'السَّبْت'
	);
    protected $months = Array(
							'مُحَرَّم',
							'صَفَر',
							'رَبِيع الأوَّل',
							'رَبِيع الثَّاني',
							'جَمَاد الأوَّل',
							'جَمَاد الثَّاني',
							'رَجَب',
							'شَعْبَان',
							'رَمَضَان',
							'شَوَّال',
							'ذُو الْقِعْدَة',
    						'ذُو الْحِجَّة',
	);
    
    protected $d;  	      // Dayweek
	protected $day; 	  // Day
	protected $month; 	  // Month
	protected $year; 	  // FullYear
    
    protected function init()
    {
    	$this->calculateCalendar();
    }
    
	private function intPart( $floatNum)
	{
		if ( $floatNum< -0.0000001) {
			return ceil( $floatNum-0.0000001);
		} else {
			return floor( $floatNum+0.0000001); 
		}		
	}
     
    protected function calculateCalendar()
    {
    	$year  = parent::get_FullYear();
    	$month = parent::get_Month();
    	$day   = parent::get_Day();
    	
		if (($year>1582)||(($year==1582)&&($month>10))||(($year==1582)&&($month==10)&&($day>14))) {
			$jd=$this->intPart((1461*($year+4800+$this->intPart(($month-14)/12)))/4)+$this->intPart((367*($month-2-12*($this->intPart(($month-14)/12))))/12)- $this->intPart( (3* ($this->intPart( ($year+4900+ $this->intPart( ($month-14)/12) )/100) ) ) /4)+$day-32075 ;
		} else {
			$jd = 367*$year-$this->intPart((7*($year+5001+$this->intPart(($month-9)/7)))/4)+$this->intPart((275*$month)/9)+$day+1729777 ;
		}
		
		$l=$jd-1948440+10632;
		$n=$this->intPart(($l-1)/10631);
		$l=$l-(10631*$n)+354;
		$j=($this->intPart((10985-$l)/5316))*($this->intPart((50*$l)/17719))+($this->intPart($l/5670))*($this->intPart((43*$l)/15238));
		$l=$l-($this->intPart((30-$j)/15))*($this->intPart((17719*$j)/50))-($this->intPart($j/16))*($this->intPart((15238*$j)/43))+29;
		$month=$this->intPart((24*$l)/709);
		$day=$l-$this->intPart((709*$month)/24);
		$year=30*$n+$j-30;
		
		$this->year  = $year;
		$this->month = $month;
		$this->day   = $day;
    }
    
	public function get_AmPm()
	{
		$ampm = (strtolower(date("a",$this->_timestamp))=='am') ? 'ق ظ' : 'ب ظ';
		return $ampm;
	}
	
	public function get_Day()
	{
		return ($this->day<10) ? '0'.$this->day : $this->day;
	}
	
	public function get_Dayweek()
	{
		return date("w",$this->_timestamp);
	}
	
	public function get_Dayweekword()
	{
		return $this->week[$this->get_Dayweek()];
	}
	
	public function get_Dayweekwordlong()
	{
		return $this->weeklong[$this->get_Dayweek()];
	}
	
	public function get_Daynotzero()
	{
		return $this->day;
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
		return ($this->month<10) ? '0'.$this->month : $this->month;
	}
	
	public function get_Monthword()
	{
		return $this->months[$this->month-1];
	}
	
	public function get_Monthwordlong()
	{
		return $this->months[$this->month-1];
	}
	
	public function get_Monthnotzero()
	{
		return $this->month;
	}
	
	public function get_FullYear()
	{
		return $this->year;
	}

	public function get_Year()
	{
		return substr($this->year,2,4);
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
