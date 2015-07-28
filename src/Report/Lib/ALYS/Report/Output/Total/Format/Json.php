<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Json extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aOutput,'a', 'aOutputaOutput');
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$trendType = $aInput['input']['flash']['type'] = isset($aInput['input']['flash']['type']) ? $aInput['input']['flash']['type'] : "trend";
		$aTmp2 = array();
		foreach($aOutput['total'] as $i => $aTmp){
			$ii = 0;
			foreach($aTmp as $metric => $v){
				$aTmp2[$i][$ii]['label'] = \YcheukfReport\Lib\ALYS\ALYSLang::_("metric_".$metric);
				$aTmp2[$i][$ii]['value'] = $v;
				$ii++;
			}
		}
		$aOutput['total.output'] = $aTmp2;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>