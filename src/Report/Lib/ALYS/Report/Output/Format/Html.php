<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Html extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aOutput['total.output'] = isset($aOutput['total.output']) ? parent::_forma_html_total($aOutput['total.output']) : "";

		$aOutput['detail.output'] = isset($aOutput['detail.output']) ? parent::_format_html_list($aOutput['detail.output']) : "";
		$sHTML = "";
		$sHTML .= isset($aOutput['flash.output']) ? $aOutput['flash.output'] : "";
		$sHTML .= "\r\n\r\n";
		$sHTML .= isset($aOutput['total.output']) ? $aOutput['total.output'] : "";
		$sHTML .= "\r\n\r\n";
		$sHTML .= isset($aOutput['detail.output']) ? $aOutput['detail.output'] : "";
		$sHTML .= "\r\n\r\n";

		$aOutput['output'] = $sHTML;

		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}

}
?>