<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail;
class Table extends \YcheukfReport\Lib\ALYS\Report\Output\Detail{

	public function __construct(){
		parent::__construct();
		
	}
	


	/**
	* 格式化成维度与指标分开
	*/
	public function _fmtDimen_Metric($type){
		
		$this->aOutput[$type] = $this->_fmtTdStyle($type);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
		
		

	}
	
	public function _fmtOutput($type='detail'){
		$this->_fmtDimen_Metric($type);
//		$this->ALYSformat($type);
		
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		
		
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.".$type.".format.".$this->aInput['output']['format']);
		$o->go();

	}	

}
?>