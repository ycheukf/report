<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Array extends \YcheukfReport\Lib\ALYS\Report\Output\Format{
	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();		
		$aArr = array();
		if(isset($aOutput['flash.output']))
			$aArr['flash.output'] = $aOutput['flash.output'];
		if(isset($aOutput['total.output']))
			$aArr['total.output'] = $aOutput['total.output'];
		if(isset($aOutput['detail.output']))
			$aArr['detail.output'] = $aOutput['detail.output'];
	
		$aOutput['output'] = $aArr;

		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}

?>