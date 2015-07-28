<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Total\Format;
class _Statichtml extends \YcheukfReport\Lib\ALYS\Report\Output\Total\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.Total.format.html");
		$o->go();
	}
}
?>