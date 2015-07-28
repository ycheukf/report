<?php
namespace YcheukfReport\Lib\ALYS\Report;

/*
* 报表引擎高阶应用
* SQL、数据集
*/
class Advance{
	
	public function __construct(){
		
	
	}
	
	//是否为高级定制
	static public function isAdvanced($type){
		$aInputData = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aInput = $aInputData['input'][$type];
		if(!empty($aInput['advanced'])){
			return true;	
		}
		return false;
	}
	//取得维度字段
	static public function getAdvanceDimens($type){
		$aInputData = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aInput = $aInputData['input'][$type];
		$aDimens = array();
//		var_dump($aInput['advanced']['dimen']);
		if(!empty($aInput['advanced']['dimen'])&&is_array($aInput['advanced']['dimen'])){
			foreach($aInput['advanced']['dimen'] as $dimens){
				if(isset($dimens['selected']))$aDimens[] = 	$dimens['selected'];
				elseif(!empty($dimens['key']))$aDimens[] = 	$dimens['key'];
			}
		}
//		var_dump($aDimens);
		return $aDimens;
	}	
	//取得指标字段
	static public function getAdvanceMetrics($type){
		$aInputData = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aInput = $aInputData['input'][$type];
		$aMetrics = array();
		if(!empty($aInput['advanced']['metric'])&&is_array($aInput['advanced']['metric'])){
			foreach($aInput['advanced']['metric'] as $metrics){
				if(!empty($metrics['key'])&&(!isset($metrics['show'])||false!==$metrics['show']))$aMetrics[] = $metrics['key'];
			}
		}
		return $aMetrics;
	}
	//取得高级定制中的sql语句 可以做必要的检查
	static public function getAdvanceSQL($type){
		$aInputData = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aInput = $aInputData['input'][$type];
		$sqls = array();
		if(!empty($aInput['advanced']['statement'])){
			$sqls = $aInput['advanced']['statement'];
			//$sql = str_replace("\n",'',$sql);
			//这里可对sql进行查检
		}
		return $sqls;
	}
	

}
?>