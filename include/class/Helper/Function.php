<?php 
class Helper_Function{
	
	public function __construct(){}
	
	public static function calcDiff($val_1,$val_2){
		return $val_1-$val_2;
	}
	
	public static function calcDiffPer($val_1,$val_2){
		
		if($val_1==0||$val_2==0)
			return 0;
		
		return sprintf('%02.2f',100*(1-($val_2/$val_1)));
	}
	
	public static function format($number){
		if(!$number){
			$number = 0;
		}
			
		return @number_format($number, 2, ',', '.');
	}
}
