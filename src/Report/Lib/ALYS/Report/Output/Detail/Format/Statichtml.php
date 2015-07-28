<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Statichtml extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.detail.format.html");
		$o->go();
	}
}