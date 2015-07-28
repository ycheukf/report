<?php
namespace YcheukfReport\Lib\ALYS\Report\Input;
class Detail extends \YcheukfReport\Lib\ALYS\Report\Input{
	protected $_type = 'detail';

	public function __construct(){

		parent::__construct();
	}

	public function preStart(){

	}

	/**
	*入口
	*/
	public function fmtInput(){
		$type='detail';
		$this->preStart();//用于各类型初始化之前的准备工作

		$this->_initaInput($type);
		$this->initInput($type);



		$this->_fmtGroup('detail');
		$this->_fmtShowBarColFlag();
	}

	/**
	* 初始化是否需要显示饼图列的参数
	*/
	public function _fmtShowBarColFlag(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$flag = false;
		if( in_array($aInput['input']['detail']['type'], array('table_bar', 'table_pie','multDate'))){
			if( in_array($aInput['output']['format'], array('html','statichtml')))
				$flag = true;
		}
		$aInput['input']['detail']['showBarColFlag'] = $flag;
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($aInput);
	}
	/**
	* 将输入的参数格式化为跨纬度指标的形式
	*/
	public function _fmtGroup($type){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$mainTable=$this->aInput['input'][$type]['mainTable']['table'];
		$aGroup = array();

		$orderByKey = null;
		$orderbyAsc = 'asc';
		if(isset($aInput['input'][$type]['orderby']) && !preg_match("/\(/i", $aInput['input'][$type]['orderby'])){
			list($orderByKey, $orderbyAsc) = @explode(' ', $aInput['input'][$type]['orderby']);
			$orderbyAsc = empty($orderbyAsc) ? 'asc' : $orderbyAsc;
		}

//				var_export($orderByKey);
//				var_export($orderbyAsc);
		foreach($aInput['input'][$type]['table'] as $table=>$aTmp){
			if(isset($aTmp['dimen']) and $table==$mainTable){
				foreach($aTmp['dimen'] as $i=>$aTmp2){
					if(!isset($aTmp2['group'])||!empty($aTmp2['group'])){
						$aTmp3 = $aTmp2;
//						$aTmp3 = array();
//						foreach($aTmp2 as $k=>$v){
//							if($k == 'key'){
//							}else{
//								$aTmp3[$k] = $v;
//							}
//						}
						$aTmp2['selected'] = empty($aTmp2['selected'])?$aTmp2['key']:$aTmp2['selected'];
						$aTmp3['key'] = $aTmp2['selected'];
						$aTmp3['sortAble'] = isset($aTmp2['sortAble']) ? $aTmp2['sortAble'] : true;
						$aTmp3['orderbyClass'] = ($orderByKey == $aTmp2['key']) ? "orderby_".$orderbyAsc : "";
						$aTmp3['thclass'] = isset($aTmp2['thclass']) ? $aTmp2['thclass'] : array("td150");
						$aTmp3['tipclass'] = array();
						$aGroup['dimen'][$aTmp2['selected']] = $aTmp3;
					}
				}
			}
			if(isset($aTmp['metric'])){
				foreach($aTmp['metric'] as $i=>$aTmp2){
					if($aTmp2['show']){
						$aTmp3 = $aTmp2;
						$aTmp3['pieFieldSelected'] = true;
						$aTmp3['pieFieldAble'] = true;
						$aTmp3['sortAble'] = isset($aTmp2['sortAble']) ? $aTmp2['sortAble'] : true;
						$aTmp3['orderbyClass'] = ($orderByKey == $aTmp2['key']) ? "orderby_".$orderbyAsc : "";
						$aTmp3['thclass'] = isset($aTmp2['thclass']) ? $aTmp2['thclass'] : array("td150");
						$aTmp3['tipclass'] = array();
						$aGroup['metric'][$aTmp2['key']] = $aTmp3;
					}
				}
			}
		}
		//print_r($aGroup);
		$aInput['groups'] = $aGroup;
		
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($aInput);
	}




}
?>