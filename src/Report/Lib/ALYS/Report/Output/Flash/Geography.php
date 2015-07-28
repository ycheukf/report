<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash;
class Geography extends \YcheukfReport\Lib\ALYS\Report\Output\Flash{

	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	* 	格式化成concatKey作为key
	*/
	public function _formatAssoc($type){
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		//echo "aMainTable=";print_r($aMainTable);
		$aDatas=array();
	
		//print_r($aMainTable['showField']);
		//print_r($this->aOutput[$type]);
		foreach($this->aOutput[$type] as $i=>$aData){
			foreach($aMainTable['showField'] as $j=>$showField){
				foreach($aData as $Data){					
					$aDatas[$i][$showField][$Data['concatKey']]=$Data[$showField];
				}
			}
			
		}
		$this->aOutput[$type]=$aDatas;
	
	}
	
	
	

}
?>