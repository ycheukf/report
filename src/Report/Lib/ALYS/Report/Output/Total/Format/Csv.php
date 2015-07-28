<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Csv extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		$separator = $this->_getCsvSeparator();
		$trendType = $aInput['input']['flash']['type'] = isset($aInput['input']['flash']['type']) ? $aInput['input']['flash']['type'] : "trend";
		$aOffsetData = $aOutput['total'];
		foreach($aOutput['total'] as $i => $aTmp){
			$compareFlag = ($i!=0)?1:0;
			foreach($aTmp as $metric => $v){
				if($compareFlag){
					if(empty($v)){
						$offerPercent = 0;
					}else{
						$offerPercent = $aOutput['total'][0][0][$metric]?(($aOutput['total'][0][0][$metric]-$v)*100/$v):0;
					}
					$aOffsetData[$i][$metric] = $offerPercent;
				}
			}
		}
		
		$sHTML = "";
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aOutput,'a', 'aOutputaOutput');
		switch(strtolower($trendType)){
			default:
			case "trend":
			case "multdate":
				$index = 0;
				foreach($aOutput['total'][0] as $metric => $vvv){
						foreach($vvv as $metric2 => $vvv2){
							if($metric != $metric2)continue;
							$sHTML .= "\r\n";
							$keyLabel = \YcheukfReport\Lib\ALYS\ALYSLang::_($metric2);
							$keyLabelTip = \YcheukfReport\Lib\ALYS\ALYSLang::_($metric2.'-tip');
							$sHTML .= '"'.$keyLabel.'"'.$separator;
							$offerPercentHTML = "";
							if($ii != 0){
								if($aOffsetData[$ii][$metric]==0)
									$offerPercentCls ="zero";
								else
									$offerPercentCls = $aOffsetData[$ii][$metric]>0?"plus2":'negative';
								$offerPercentLabel = abs(round($aOffsetData[$ii][$metric], 2))."%";
								$offsetPercentHTML = '('.$offerPercentLabel.')';
								$sHTML .= '"'.$vvv2.''.$offsetPercentHTML.'"'.$separator;
							}else{
								$sHTML .= '"'.$oDict->ALYSmetricFormat($metric2, $vvv2).'"'.$separator;
							}
						}

					$index++;
				}
				break;
		}
		$sHTML .= "\r\n";
//		var_export($sHTML);
		$aOutput['total.output'] = $sHTML;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>