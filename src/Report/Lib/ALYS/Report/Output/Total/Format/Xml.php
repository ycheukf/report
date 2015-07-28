<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Xml extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aOutput,'a', 'aOutputaOutput');
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$trendType = $aInput['input']['flash']['type'] = isset($aInput['input']['flash']['type']) ? $aInput['input']['flash']['type'] : "trend";
		$aOffsetData = $aOutput['total'];
		foreach($aOutput['total'] as $i => $aTmp){
			$compareFlag = ($i!=0)?1:0;
			foreach($aTmp as $metric => $v){
				if($compareFlag){
					if(empty($v))
						$offerPercent = 0;
					else
						$offerPercent = $aOutput['total'][0][$metric]?(($aOutput['total'][0][$metric]-$v)*100/$v):0;
					$aOffsetData[$i][$metric] = $offerPercent;
				}
			}
		}
		
		$sHTML = "";
//		var_export($aOffsetData);
		switch(strtolower($trendType)){
			default:
			case "trend":
			case "multdate":
				$index = 0;
				foreach($aOutput['total'][0] as $metric => $vvv){
						foreach($vvv as $metric2 => $vvv2){
							if($metric != $metric2)continue;
							$sHTML .= "<entity>\n";
							$keyLabel = \YcheukfReport\Lib\ALYS\ALYSLang::_($metric2);
							$keyLabelTip = \YcheukfReport\Lib\ALYS\ALYSLang::_($metric2.'-tip');
							$sHTML .= '<label>'.$keyLabel."</label>\n";
							$offerPercentHTML = "";
							if($ii != 0){
								if($aOffsetData[$ii][$metric]==0)
									$offerPercentCls ="zero";
								else
									$offerPercentCls = $aOffsetData[$ii][$metric]>0?"plus2":'negative';
								$offerPercentLabel = abs(round($aOffsetData[$ii][$metric], 2))."%";
								$offsetPercentHTML = '('.$offerPercentLabel.')';
								$sHTML .= '<value>'.$vvv2.','.$offsetPercentHTML."</value>\n";
							}else{
								$sHTML .= '<value>'.$vvv2."</value>\n";
							}
							$sHTML .= "</entity>\n\n";
					}
					$index++;
				}
				break;
		}
		$sHTML .= "\r\n";
		$aOutput['total.output'] = "<total>\n".$sHTML."</total>\n";
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>