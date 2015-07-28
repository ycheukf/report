<?php
namespace YcheukfReport\Lib\ALYS\Report\Output;
class Total extends \YcheukfReport\Lib\ALYS\Report\Output{

	public function __construct(){
		
		parent::__construct();
		
	}

	/**
	* 	格式化成concatKey作为key
	*/
	public function _formatAssoc($type){
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		//echo "aMainTable['showField']=";print_r($aMainTable['showField']);
		$aDataN=array();
		//echo "this->aOutput[$type]=";print_r($this->aOutput[$type]);
		foreach($this->aOutput[$type] as $date_i=>$aDatas){
			foreach($aDatas as $i=>$aData){
				foreach($aMainTable['showField'] as $j=>$showField){				
					$aDataN[$date_i][$i][$showField]=$aData[$showField];
				}
			}
			
		}
		$this->aOutput[$type]=$aDataN;
		//echo "this->aOutput[$type]=";print_r($this->aOutput[$type]);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	
	}
	

	
	public function fmtOutput(){
		$type='total';
		$this->_formatAssoc($type);
		
		
		//$this->_fmtOutput();
		$this->_fmtOutput($type);
	}


}
?>