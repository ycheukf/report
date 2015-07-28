<?php
/**
* 
* 通用函数
*/
namespace YcheukfReport\Lib\ALYS;

class ALYSFormat{

	/**
	* 将秒数转化为时间格式
	*/
	static function second4Time($time){
		$sRe = '';
		if($time >= 86400){
			$day = intval($time/86400);
			$time = $time%86400;
			$sRe = $day.'天 '.$sRe;
		}
		$sRe = $sRe.date('H:i:s',  mktime( 0, 0, $time));
		return $sRe;
	}
	
	/**
	* 格式化成百分数
	*/
	static function percent($f){
		return $f = (round($f*100,2)).'%';
	}
	
	/**
	* 格式化成2位小数
	*/
	static function round($f){
		return $f=round($f,2);
	}
	
	/** 
	* 格式化千分号数字 保留2位
	*/
	public static function formatNumber($num){
		$num = number_format($num,2);
		$num = str_replace('.00','',$num);//去掉后边多余的0
		return $num;
	}	
}

?>
