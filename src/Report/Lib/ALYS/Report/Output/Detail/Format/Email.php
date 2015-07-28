<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Email extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.detail.format.statichtml");
		$o->go();
	}
}
