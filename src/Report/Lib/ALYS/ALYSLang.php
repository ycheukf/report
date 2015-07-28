<?php
namespace YcheukfReport\Lib\ALYS;

class ALYSLang{
	static public $aDict;
	function __construct(){
	}	
	public static function _($s, $aReplace = array(), $returnFlag=1){
		$s = strtoupper($s);
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("lang");
		$sLangVal = $oPlugin->ALYSbefore_translation($s);
		if($sLangVal == $s){
			$aDict = self::getDict();
			$s = isset($aDict[$s]) ? $aDict[$s] : $s;
			if(is_array($aReplace)){
				for($i=0; $i<count($aReplace); $i++){
					$s = str_replace("[replace{$i}]", $aReplace[$i], $s);
				}
			}elseif(is_string($aReplace)){
				$s = str_replace("[replace0]", $aReplace, $s);
			}
			$sLangVal = $s;
		}
		if($returnFlag)
			return $sLangVal;
		else 
			echo $sLangVal;
	}
	static function getDict(){
		$_ALYSlang = \YcheukfReport\Lib\ALYS\Lang\Cn::getDict();
		if(isset($_ALYSlang))
			self::$aDict = $_ALYSlang;
		return self::$aDict;
	}
}

?>
