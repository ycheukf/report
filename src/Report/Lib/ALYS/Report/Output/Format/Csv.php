<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Csv extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("csv");
		$sCsv = $oPlugin->ALYSfmtOutputCsv($aOutput);
		$aOutput['output'] = $sCsv;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}

}
?>