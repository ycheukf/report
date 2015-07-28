<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Xml extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		
		$sHtml = "";
		if(isset($aOutput['flash.output']))
			$sHtml .= $aOutput['flash.output']."\n\n";
		if(isset($aOutput['total.output']))
			$sHtml .= $aOutput['total.output']."\n\n";
		if(isset($aOutput['detail.output']))
			$sHtml .= $aOutput['detail.output']."\n\n";

		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("xml");
		$sTitle = $oPlugin->ALYSxml_title();		
		$aOutput['output'] = "<?xml version='1.0' encoding='UTF-8'?>\n<report>\n\n".$sTitle.$sHtml."\n\n</report>";
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>