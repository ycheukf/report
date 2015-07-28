<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Array extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		
		$aOutput['detail.output'] = isset( $aOutput['detail']) ? $aOutput['detail'] : null;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>