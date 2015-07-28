<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail;
class Tablepie extends \YcheukfReport\Lib\ALYS\Report\Output\Detail{

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
		//配置
		$totalPercent = 100;//饼图总共份额
		$iRound = 2;//小数点后保留位数
		$iMaxDisplay = 10;//饼图最大显示数据条数
		
		$sSelectedMetric = $this->aInput['input']['detail']['selected'];//当前选择的指标名
		$sTotalMetric = $this->aInput['input']['detail']['totalselected'];//total的字段名 用于计算百分比
		
		//颜色
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin('detail');
		$aBgColor = $oPlugin->ALYSgetListBgColor();
		
		//没有设置百分比选项,使用默认
		if(!isset($sSelectedMetric)){  
			$keys = array_keys($this->aInput['groups']['metric']);
			$sSelectedMetric = $keys[0];
		}
		
		//total的字段名
		if(isset($sTotalMetric)&&!empty($sTotalMetric)){
			$sTotalSelectedMetric = $sTotalMetric;
		}else{
			$sTotalSelectedMetric = preg_replace('/_nosum$/','',$sSelectedMetric);//去掉最后的“不加”标识
		}


		//显示在饼图上的内容 维度字段名
		$dimen = $this->aInput['input']['detail']['pieDisplay'];
		if(!isset($dimen) or $dimen == null){
			$dimen=$this->aDimen[0];
		}
			
		
		//var_dump($this->aOutput[$type]);exit;
		//初始化
		$i=0;//条数计数器
		$fCurrentPercentSum = 0;//当前页当前指标百分比累加的总数
		$fCurrentSum = 0;//当前页当前指标累加的总数
		$iMax=0;//最大数值 设置isSliced='1'
		$aData = array();
		$aOrgData = $this->aInput['internal'][$type]['datas'];//原始数据 用于计算
		
		//取得total总和
		$sum = $this->aInput['internal']['total']['datas'][0][0][$sTotalSelectedMetric];

		//计算伪指标的百分比
		if(is_array($this->aOutput[$type])){
			foreach($this->aOutput[$type] as $k => $v){
				foreach($v as $kk => $vv){
					$tmp = array();
					if($i>($iMaxDisplay-1)){
						break;//超出最大条数 跳出
					}
					
					$tmp['percent'] = ($sum>0?round((($aOrgData[$k][$kk][$sSelectedMetric]/$sum)*100),$iRound):0);
					$fCurrentPercentSum+=$tmp['percent'];
					$fCurrentSum+=$aOrgData[$k][$kk][$sSelectedMetric];
					$tmp['dimen'] = \YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($vv[$dimen]);
					$tmp['metric'] = $aOrgData[$k][$kk][$sSelectedMetric];
					$tmp['metricDisplay'] = $vv[$sSelectedMetric];
					$tmp['color'] = $aBgColor[$i];
					$aData[] = $tmp;
					if($tmp['metric']>$iMax){
						$iMax = $tmp['metric'];
					}
					
					$i++;
				}
			}
		}
		//"其它" 的数据
		if(($totalPercent-$fCurrentPercentSum)>0){
			$tmp['percent'] = round($totalPercent-$fCurrentPercentSum,$iRound);
			$tmp['dimen'] = ALYSLANG::_('other');
			$tmp['metric'] = ($sum-$fCurrentSum)>0?($sum-$fCurrentSum):0;
			$tmp['metricDisplay'] = $tmp['metric'];
			$tmp['color'] = $aBgColor[$i];
			if($tmp['metric']>$iMax){
				$iMax = $tmp['metric'];
			}
			if($tmp['metric']>0){
				$aData[] = $tmp;
			}
		}
		
		//组织xml
		$pie_datasets = $pie_grid_datasets = '';
		if(is_array($aData)){
			foreach($aData as $v){
				$isSliced = $v['metric']>=$iMax?" isSliced='1'":'';
				$pie_datasets .= "<set value='".$v['percent']."'".$isSliced." label='".$v['dimen']."' color='".$v['color']."' displayValue='".$v['dimen'].",".$v['metricDisplay']."' tooltext='".$v['dimen']."{br}".$v['percent']."%' />";
				$pie_grid_datasets .= "<set value='".$v['metric']."'".$isSliced." label='".$v['dimen']."' color='".$v['color']."' displayValue='".$v['dimen'].",".$v['metricDisplay']."' tooltext='".$v['dimen']."{br}".$v['percent']."%' />";
			}	
		}
		
		$iDataCnt = count($aData);
		
		//取得样式
		$afusion = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Flash");
		$style = $afusion->ALYSoutput_flash_trend_html_style();
		$this->aChartStyles = $style[0]['pie3D'];
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		foreach($this->aChartStyles as $k => $v){
			$s1 .= " {$k}='{$v}'";
		}
		
		$xmlDataPie = "<chart {$s1}>".$pie_datasets."</chart>";
		$xmlDataPieGrid = "<chart {$s1}>".$pie_grid_datasets."</chart>";
		
		$height = $afusion->ALYSoutput_flash_pie3D($iDataCnt);  //获取饼图高度
		$aHtml = array();
		$aHtml = $afusion->ALYSfmtOutputFusionScript($xmlDataPie, 'pie3D', $height['pie3D']);
		$aHtml2 = $afusion->ALYSfmtOutputFusionScript($xmlDataPieGrid, 'SSGrid', $height['SSGrid']);
		$this->aOutput['detail.pieScript'] = $aHtml[0];
		$this->aOutput['detail.pieGridScript'] = $aHtml2[0];
		//组织伪指标中的内容
		foreach($this->aOutput[$type] as $k => $v){
			$index2 = 0;
			foreach($v as $kk => $vv){
				$this->aOutput[$type][$k][$kk][$this->sThLabel] = $k==0&&$index2==0 ? $aHtml[0].$aHtml2[0] : "";
				$index2++;
			}
		}
//		var_export($this->aOutput[$type]);
		
		//为th增加一列伪指标:百分比
		array_push($this->aMetric, $this->sThLabel);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}

}