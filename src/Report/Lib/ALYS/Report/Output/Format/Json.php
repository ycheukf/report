<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Json extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		
		$aJson = array();
		if(isset($aOutput['flash.output']))
			$aJson['flash'] = $aOutput['flash.output'];
		if(isset($aOutput['total.output']))
			$aJson['total'] = $aOutput['total.output'];
		if(isset($aOutput['detail.output']))
			$aJson['detail'] = $aOutput['detail.output'];
	
		$aOutput['output'] = json_encode($aJson);

		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>