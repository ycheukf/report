<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Array extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{
	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();	  		
		$aOutput['flash.output'] = isset($aOutput['flash']) ? $aOutput['flash'] : null;
		
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}

?>