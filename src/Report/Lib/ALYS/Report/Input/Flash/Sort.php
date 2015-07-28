<?php
namespace YcheukfReport\Lib\ALYS\Report\Input\Flash;
class Sort extends \YcheukfReport\Lib\ALYS\Report\Input\Flash{

	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	* 特殊检测的处理
	* 钩子
	*/
	public function chkInputParam($type){
		$aTables=$this->aInput['input']['detail'];
		if(count($aTables)<=0){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_depend','detail');
		}
	}
	
	/**
	*扩展父类中的输入参数
	*/
	public function fmtInput(){

		parent::fmtInput();
		$type='flash';
		$aDefaultIndex = array();
		for($i=0; $i<$this->aInput['input']['detail']['page']['total']; $i++){
			$aDefaultIndex[] = $i;
		}
		if(isset($this->aInput['input'][$type]['sort_indexs'])){//用户传递了列表的参数
			$this->aInput['input'][$type]['sort_indexs']  =  count($this->aInput['input'][$type]['sort_indexs']) < $this->aInput['input']['detail']['page']['total'] ? $this->aInput['input'][$type]['sort_indexs'] : $aDefaultIndex;
		}else{//默认
			$this->aInput['input'][$type]['sort_indexs'] = ($this->aInput['input']['detail']['page']['total']>4 ? array(0,1,2,3,4) : $aDefaultIndex);
		}
		//初始化 sort模式下的几个默认选中排列项目
		$this->aInput['input'][$type]['sort_totalflag']  = isset($this->aInput['input'][$type]['sort_totalflag']) ? $this->aInput['input'][$type]['sort_totalflag'] : 0;
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
//		var_export($this->aInput['input']['detail']['page']['total']);
	}
}
?>