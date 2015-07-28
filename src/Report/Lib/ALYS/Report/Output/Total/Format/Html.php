<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Html extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$type='total';
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aOffsetData = $aOutput['total'];
		foreach($aOutput['total'] as $date_i =>$aData){
			foreach($aData as $i => $aTmp){
				$compareFlag = ($date_i!=0)?1:0;
				foreach($aTmp as $metric => $v){
					if($compareFlag){
						if(empty($v))
							$offerPercent = 0;
						else
							$offerPercent = $aOutput['total'][0][0][$metric] ? (($aOutput['total'][0][0][$metric]-$v)*100/$v) : 0;
						$aOffsetData[$date_i][$metric] = $offerPercent;
					}
				}
			}
		}
		$this->_formatMetric($type);  //格式化

		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("total");
		$aOutput['total.output'] = 	$oPlugin->ALYSfmtOutputHtml($aOffsetData);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}

	
}
//?>