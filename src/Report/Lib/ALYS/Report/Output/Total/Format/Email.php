<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Email extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.Total.format.statichtml");
		$o->go();
	}
}
?>