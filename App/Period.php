<?php 

namespace App;

class Period{
	
	public static function getCurrentYear(){
		 $currentYear=date('Y');
		 return $currentYear;
	}
	
	public static function getCurrentMonth(){
		return date('m');
	}
	
	public static function setPreviousMonth(){
		if(date('m')==1) $prevMonth=12;
		else $prevMonth=date('m')-1;
		return $prevMonth;
	}
	
	public static function setCurrentYM(){
		$currentYM=static::getCurrentYear().'-'.static::getCurrentMonth().'-01';
		return $currentYM;
	}
	
	public static function setPreviousYM(){
		$prevYM=static::getCurrentYear().'-'.static::setPreviousMonth().'-01';
		return $prevYM;
	}
	
	public static function setPreviousYMEnd(){
		$prevYMEnd=static::getCurrentYear().'-'.static::setPreviousMonth().'-31';
		return $prevYMEnd;
	}

}