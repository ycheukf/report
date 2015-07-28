<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Json extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aJson = array();

		$aReturn = "";
		foreach($aOutput['flash'] as $i=> $aTmp){
			foreach($aTmp as $x=> $aTmp2){
				foreach($aTmp2 as $metric=> $v){
					$aReturn[$i][$x]['x'] = $x;
					$aReturn[$i][$x]['y'] = \YcheukfReport\Lib\ALYS\ALYSLang::_("metric_".$metric);
					$aReturn[$i][$x]['v'] = $v;
				}
			}
		}
		$aOutput['flash.output'] = $aReturn;
		
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>