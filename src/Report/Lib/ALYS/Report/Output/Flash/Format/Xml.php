<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Xml extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$trendType = $aInput['input']['flash']['type'] = isset($aInput['input']['flash']['type']) ? $aInput['input']['flash']['type'] : "trend";
		$xmlDataEntity = "";
		if('bubble'==$trendType){
			$xmlDataEntity .= "\n\r<bubble>";
			foreach($aOutput['flash'] as $i=> $aTmp){
				$xmlDataEntity .= "\n\r<entity>\n";
				$xmlDataEntity .= "<column>".$i."</column>\n";
				foreach($aTmp as $date=> $aTmp2){
					$xmlDataEntity .= "<column>".$aTmp2."</column>";
				}
				$xmlDataEntity .= "\n</entity>";
			}
			$xmlDataEntity .= "\n\r</bubble>";
		}else{
			foreach($aOutput['flash'] as $i=> $aTmp){
				$xmlDataEntity .= "\n\r<trend>";
				foreach($aTmp as $date=> $aTmp2){
					$xmlDataEntity .= "\n\r<entity>\n";
					$xmlDataEntity .= "<column>".$date."</column>\n";
					$xmlDataEntity .= "<column>".current(array_values($aTmp2))."</column>";
					$xmlDataEntity .= "\n</entity>";
				}
				$xmlDataEntity .= "\n\r</trend>";
			}
		}
		$aOutput['flash.output'] = "<flash>\n\n".$xmlDataEntity."</flash>\n\n";
		
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($aInput);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>