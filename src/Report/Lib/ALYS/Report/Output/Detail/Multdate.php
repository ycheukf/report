<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail;
class Multdate extends \YcheukfReport\Lib\ALYS\Report\Output\Detail{

	public function __construct(){
		parent::__construct();
		
	}
	

	public function _fmtOutput(){
//		$this->ALYSformat($type);
		$this->_getPercent();
		$type='detail';
		$this->aOutput[$type] = $this->_fmtTdStyle($type);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
		
		//$aOutput['detail'] = $this->_fmtData();

		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.".$type.".format.".$this->aInput['output']['format']);
		$o->go();
	}
	

}
?>