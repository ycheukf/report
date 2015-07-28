<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Pdf extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.Flash.format.html");
		$o->go();
	}
}
?>