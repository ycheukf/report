<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Array extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();	  		
		$aOutput['total.output'] = isset($aOutput['total']) ? $aOutput['total'] : null;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>