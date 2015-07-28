<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Html extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($this->aOutput, 'a', 'www');
		$flashHtmlFormat = isset($this->aInput['output']['flashHtmlFormat']) ? $this->aInput['output']['flashHtmlFormat'] : 'fusion';
		$oHtmlFormat = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("Report.Output.Flash.Format.Html.".$flashHtmlFormat.".".$this->aInput['input']['flash']['type']);
		$oHtmlFormat->go();
	}
}
//?>