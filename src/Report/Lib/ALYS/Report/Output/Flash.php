<?php
namespace YcheukfReport\Lib\ALYS\Report\Output;
class Flash extends \YcheukfReport\Lib\ALYS\Report\Output{

	public function __construct(){
		
		parent::__construct();
		$this->aOutput['flash.seriestips'] = $this->aInput['input']['flash']['seriestips'];
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}

	/**
	* 	格式化成concatKey作为key
	*/
	public function _formatAssoc($type){
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		//echo "aMainTable=";print_r($aMainTable);
		$aDatas=array();
		$countField=count($aMainTable['showField']);
		//print_r($aMainTable['showField']);
				
		foreach($this->aOutput[$type] as $i=>$aData){
			foreach($aMainTable['showField'] as $j=>$showField){
				$k=$countField*$i+$j;
				foreach($aData as $Data){		
					$concatKey=$Data['concatKey'];
					if($this->aInput['input'][$type]['groupby']=='hour') $concatKey .=":00";
					$aDatas[$k][$concatKey][$showField]=$Data[$showField];
				}
			}
			
		}
		$this->aOutput[$type]=$aDatas;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}
		
	public function fmtOutput(){
		$type='flash';
		$this->_initDimen_Metric($type);
		$this->_formatAssoc($type);
		
		//以日期作为横向指标的情况, 决定用何种补全数据方式
		if($this->aInput['input']['flash']['type'] <> 'geography') $this->_fillTrendZero($type);
//		}
		$this->_formatMetric($type);
		$this->_fmtOutput($type);
		//\YcheukfReport\Lib\ALYS\ALYSFunction::debug($this->aOutput,'a',"this->aOutput[$type]");
//		return $this->aOutput;
	}

	/**
	*  判断维度是否为日期相关
	*/
	public function getDimenTimeType($type, $dimenKey){
		//data作为横向指标时 key为下列字段
		$aInput = $this->aInput['input'][$type];
		$mainTable=$aInput['mainTable']['table'];
		foreach($aInput['table'][$mainTable]['dimen'] as $aTmp){
			if($aTmp['key'] == $dimenKey)
				return $aTmp['timeslotType'];
		}
		return 0;
	}

	/**
	* 补全数据, 为趋势图数据补零
	*/
	public function _fillTrendZero($type){
		$mainTable = $this->aInput['input'][$type]['mainTable']['table'];
		$aDimens = $this->aInput['input'][$type]['tables'][$mainTable]['dimen']; //维度
		$nTotalMetric=count($this->aMetric);	
		//\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aDimens,'a', __CLASS__.'/'.__FUNCTION__.'/'.__LINE__);
		
		if(count($aDimens['key']) > 1)
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSFLASH_CONFIG_WRONG', 'can not set more than one group dimen at flash. '.__FILE__.'/'.__LINE__);

		$timeslotType = $this->getDimenTimeType($type, $aDimens['key'][0]);
		$sSelected = $aDimens['dimenkey2selected'][$aDimens['key'][0]];

		$aTrendConfig = array();
		$iTrend = 0;
		switch($this->aInput['input'][$type]['type']){
			case 'multDate':
				foreach($this->aInput['date'] as $i =>$aDate){//循环多时段
					$aTrendConfig[$iTrend]['date'] = $aDate;
					$aTrendConfig[$iTrend]['metric'] = $this->aMetric[0];
					$iTrend++;
				}
			break;
			case 'multMetric':
				foreach($this->aMetric as $i=>$metric){//循环多指标
					$aTrendConfig[$iTrend]['date'] = $this->aInput['date'][0];
					$aTrendConfig[$iTrend]['metric'] = $metric;
					$iTrend++;
				}
			break;
			case 'trend':
				$aTrendConfig[$iTrend]['metric'] = $this->aMetric[0];
				$aTrendConfig[$iTrend]['date'] = $this->aInput['date'][0];
				$iTrend++;
			break;
			case 'sort':
				switch($this->aInput['input']['detail']['type']){
					case 'perspective':
						 foreach($this->aInput['input'][$type]['sort_indexs'] as $nSort){
							$aTrendConfig[$iTrend]['metric'] = $this->aMetric[0];
							$aTrendConfig[$iTrend]['date'] = $this->aInput['date'][0];
							$aTrendConfig[$iTrend]['xdimen'] = $this->aInput['internal']['xdimen'];
							$iTrend++;
						}
					break;
					default:
					case 'sort':
					 foreach($this->aInput['input'][$type]['sort_indexs'] as $nSort){
						$aTrendConfig[$iTrend]['metric'] = $this->aMetric[0];
						$aTrendConfig[$iTrend]['date'] = $this->aInput['date'][0];
						$iTrend++;
					}
					break;
				}
			break;
			case 'geography':
			default:
				return false;
			break;
		}
//
//var_dump($aTrendConfig);
		foreach($aTrendConfig as $i=>$aConfig){
			$aFlashDataNew = $aFlashDataTmp=array();		
			$aFlashDataNew = isset($this->aOutput[$type][$i]) ? $this->aOutput[$type][$i] : array();
			$metric = $aConfig['metric'];
			$nextDay = $aConfig['date']['s'];
			$eDate = $aConfig['date']['e'];

			if($timeslotType==1){//日期补全
				while($eDate >= $nextDay){

					$sNextSlot = $timeSolt = $this->_getTimeSlot($nextDay, $sSelected);
					// if($sSelected == 'week'){
						// $sNextSlot = $timeSolt.$this->splitChar.$aMultTrendConfig['sDate'].$this->splitChar.$aMultTrendConfig['eDate'];
					// }
					if(isset($aFlashDataNew[$sNextSlot])){
						$aFlashDataTmp[$sNextSlot] = $aFlashDataNew[$sNextSlot];
					}else{//若该天没有数据则补零
						$aFlashDataTmp[$sNextSlot][$metric] = 0;
						$aFlashDataTmp[$sNextSlot][$sSelected] = $sNextSlot;
					}
					$nextDay  = date("Y-m-d", strtotime("+1 day", strtotime($nextDay)));
				}
			}elseif($timeslotType==2){//小时
				for($ii=0; $ii<24; $ii++){
					$hour = $ii.":00";
					if(isset($aFlashDataNew[$hour][$metric])){
						$aFlashDataTmp[$hour] = $aFlashDataNew[$hour];
					}else{
						$aFlashDataTmp[$hour][$metric]=0;
						$aFlashDataTmp[$hour][$sSelected]=$hour;
					}
				}
			}
			else{
				if(isset($aConfig['xdimen'])){//透视图补全
					foreach($aConfig['xdimen'] as $sDimenTmp){
						$aFlashDataTmp[$sDimenTmp][$metric] = isset($aFlashDataNew[$sDimenTmp][$metric]) ? $aFlashDataNew[$sDimenTmp][$metric] : 0;
					}
				}else{
					$aFlashDataTmp = isset($this->aOutput[$type][$i]) ? $this->aOutput[$type][$i] : array();
				}
			}
			$this->aOutput[$type][$i]=$aFlashDataTmp;
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);

				
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($this->aOutput,'a', __CLASS__.'/'.__FUNCTION__.'/'.__LINE__);
		
	}
}
?>