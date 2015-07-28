<?php

class ALYSHTML_Button{
	function __construct(){		
//		parent::__construct();
	}
	function _getCssName($className){
		return 'ALYShtml_button_'.$className;
	}

	/**
	* 只有图片的按钮
	*/
	public static function pic($className="", $return=false){
		$className = self::_getCssName($className);
		$s = <<<OUTPUT
			<div class='{$className}'>some html</div>
OUTPUT;
		if($return)return $s;
		else echo $s;
	}


	/**
	* 图片与文字的按钮
	*/
	public static function picText($className="", $return=false){
		$className = self::_getCssName($className);
		$s = <<<OUTPUT
			<div class='{$className}'>some html</div>
OUTPUT;
		if($return)return $s;
		else echo $s;
	}
}

//ALYSHTML_Button::pic('a');

ALYSHTML_Button::picText('b');