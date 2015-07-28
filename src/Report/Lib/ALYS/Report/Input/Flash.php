<?php
namespace YcheukfReport\Lib\ALYS\Report\Input;
class Flash extends \YcheukfReport\Lib\ALYS\Report\Input{

	public function __construct(){
		
		parent::__construct();
	}
	/**
	*入口
	*/
	public function fmtInput(){
		$type='flash';
		$this->_initaInput($type);
		$this->initInput($type);
		$this->_setFlashType($type);
		
		//初始化 默认flash的seriestips
		$this->aInput['input'][$type]['seriestips'] = $this->aInput['input'][$type]['mainTable']['showField'];

		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);		
	}
//	public function initInput($type){
//		parent::initInput($type);
//		var_export($this->aInput['input'][$type]['mainTable']['showField']);
//	}
	/**
	* set date type
	*/
	public function _setFlashType($type){
		$aTmpField=array();
		$aDate=$this->aInput['date'];
		if(count($aDate)>1){
			$this->aInput['input'][$type]['type']='multDate';
		}else{
			if($type=='flash'){
				$aTables=$this->aInput['input']['flash']['table'];
				if(is_array($aTables)){
					foreach($aTables as $aTable){
						foreach($aTable['metric'] as $aMetric){
							if($aMetric['show']){
								$aTmpField[]=$aMetric['key'];
							}
						}
						break;
					}
				}
				if(count($aTmpField)>1){
					$this->aInput['input'][$type]['type']='multMetric';
				}
			}
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}
}
?>