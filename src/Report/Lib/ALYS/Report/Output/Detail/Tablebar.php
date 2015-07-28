<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail;
class Tablebar extends \YcheukfReport\Lib\ALYS\Report\Output\Detail{

	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	* 格式化成维度与指标分开
	*/
	public function _fmtDimen_Metric($type){
		if(in_array($this->aInput['output']['format'],array('html','statichtml'))){
			$this->_percent($type);
		}
		
		$this->aOutput[$type] = $this->_fmtTdStyle($type);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}
	
	public function _fmtOutput(){
		$type='detail';
		$this->_fmtDimen_Metric($type);
		
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.".$type.".format.".$this->aInput['output']['format']);
		$o->go();

	}	
	/**
	* 为柱状条格式化TD 属性
	*/
	public function _percent($type){

		$sSelectedMetric = $this->aInput['input']['detail']['selected'];
		$sTotalMetric = $this->aInput['input']['detail']['totalselected'];//total的字段名 用于计算百分比
		
		if(!isset($sSelectedMetric)){  //没有设置百分比选项,使用默认
			$keys = array_keys($this->aInput['groups']['metric']);
			$sSelectedMetric = $keys[0];
		}
		
		//total的字段名
		if(isset($sTotalMetric)&&!empty($sTotalMetric)){
			$sTotalSelectedMetric = $sTotalMetric;
		}else{
			$sTotalSelectedMetric = preg_replace('/_nosum$/','',$sSelectedMetric);//去掉最后的“不加”标识
		}
		
		/*
		$temps = array();
		foreach($this->aOutput[$type] as $k => $v){
			foreach($v as $kk => $vv){
				$temps[] = $vv[$sSelectedMetric];
			}
		}
		$sum = array_sum($temps);
		*/

		//计算总和
		
		//取得total总和
		$sum = $this->aInput['internal']['total']['datas'][0][0][$sTotalSelectedMetric];

		//组织伪指标中的内容
		$aOrgData = $this->aInput['internal'][$type]['datas'];//原始数据 用于计算
		foreach($this->aOutput[$type] as $k => $v){
			foreach($v as $kk => $vv){
				$percent = ($sum>0?round((($aOrgData[$k][$kk][$sSelectedMetric]/$sum)*100),2):0)."%";
				$this->aOutput[$type][$k][$kk][$this->sThLabel] = "<div class='tbbar'><div  style='width:$percent'><span >$percent</span></div></div>";
			}
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
		
		//为th增加一列伪指标:百分比
		array_push($this->aMetric, $this->sThLabel);
//		var_export($this->aMetric);
	}

}
?>