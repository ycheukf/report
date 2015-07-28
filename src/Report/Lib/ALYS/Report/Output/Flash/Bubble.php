<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash;
class Bubble extends \YcheukfReport\Lib\ALYS\Report\Output\Flash{

	public function __construct(){
		parent::__construct();
		
	}
	
	public function fmtOutput(){
		$type='flash';
		$this->_initDimen_Metric($type);
		
		$this->_formatAssoc($type);
	
		$this->_fmtOutput($type);
	}
	
	/**
	* 	格式化成concatKey作为key
	*/
	public function _formatAssoc($type){
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		//echo "aMainTable=";print_r($aMainTable);
		$aDatas=array();
		$countField=count($aMainTable['showField']);
		$aOData = empty($this->aOutput[$type][0])?array():$this->aOutput[$type][0];
		if(is_array($aOData)){
			foreach($aOData as $i=>$aData){
				$concatKey=$aData['concatKey'];
				unset($aData['concatKey']);
				$aDatas[$concatKey] = $aData;
			}
		}
		$this->aOutput[$type]=$aDatas;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}

}
?>