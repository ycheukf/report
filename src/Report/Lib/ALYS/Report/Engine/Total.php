<?php
namespace YcheukfReport\Lib\ALYS\Report\Engine;
class Total extends \YcheukfReport\Lib\ALYS\Report\Engine{

	public function __construct(){
		parent::__construct();
		
	}
	/**
	* 获得数据入口
	*/
	public function getData(){
		$type='total';
		if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced($type)){
			
			$aData = $this -> getAdvancedData($type);
		}else{
			$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
			$aConf=$this->getConf($type);
			$aDatesNew=$this->getDateNew($type);
			$aDates=$this->getDate($type);
			$aConfs=array();
			
			foreach($aDates as $k=>$Date){
				foreach($aConf as $table =>$Conf){
					$aConfs[$k][$table]=$Conf;
					
					$aCondition=$Conf['condition'];
					if(isset($aInput['nodateFlag']) && !$aInput['nodateFlag']){//have date
						$aCondition=array_merge(array($aDatesNew[$k][$table]),$aCondition);
					}
//					$aCondition=array_merge(array($aDatesNew[$k][$table]),$aCondition);
					$aConfs[$k][$table]['condition']=$aCondition;
				}
			}
			//\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aConfs, 'a', 'aConfs');
			foreach($aConfs as $k =>$aConf){
				foreach($aConf as $table=>$Conf){
					list($aData[$k][$table], $nTmpTotal) = $this->$type->getAlldata($Conf);
				}
			}
		}
		
		$aData=$this->_executeSqlData($type,$aData);
		return $aData;
	}
	

	
	/**
	* init input param
	*/
	/**
	根据数组设置搜索条件
	@parm array aConf
	@example 数组格式
		array{ 
			'noRecord' => 0,	//1=>不执行sql语句, 直接返回空
			'limit' => 1,	//1=>使用limit,0=>不使用
			'start' => 0, 
			'length' => 20, 
			'orderby' => 'id', 
			'orderbyDesc' => 1, 
			'groupby' => 'id', 
			'table' =>'user', 
			'field' =>'id, name' 
			'condition' => array('id=?'=>1, 'name=?'=>'MIC') 
			'conditionOr' => array('id=?'=>1, 'name=?'=>'MIC') 
		}
	*/
//SELECT SQL_CALC_FOUND_ROWS sum(pageView) as pageView,sum(visitor) as visitor,sum(sumDepth)/sum(sumSession) as avgDepth,sum(sumTimeLength)/sum(sumSession) as avgTimeLength,sum(sumSession) as sumSession,sum(newPageView) as newPageView,sum(newVisitor) as newVisitor,sum(uniqueIP) as uniqueIP,(sum(visitor)-sum(newVisitor))/sum(visitor) as visiteBack FROM StatsPageviewAll WHERE (((date >= '2011-11-10' and date <= '2011-11-14')) and advertiserId in (84) and domainId in (-1, 1418, 1419, 1420, 1422, 1437, 1441, 1446, 1447, 1450, 1453, 1460, 1464, 1470, 1475, 1489, 1503, 1510, 1513, 1519, 1542, 1543, 1546, 1588, 1608, 1611, 1614, 1619, 1632, 1633, 1669, 1674, 1719, 1755, 1788, 1822, 1835, 1916, 1962, 2009, 2089, 2094, 2134, 2152, 2154, 2156, 2198, 2204, 2283, 2284, 2401, 2411, 2505, 2530, 2543, 2553, 2564, 2621, 2623, 2624, 2632, 2668, 2688, 2715, 2722, 2734, 2748, 2751, 2790, 2792, 2827, 2963, 3008, 3120, 3134, 3191, 3195, 3199, 3207, 3270, 3271, 3297, 3307, 3310, 3349, 3357, 3364, 3390, 3480, 3485, 3493, 3528, 3593, 3453, 3478, 3494, 3544, 3547, 3564))
	public function getConf($type){
		$aConf=array();
		if(!count($this->aInput['input'][$type]['table']))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_TABLE_EMPTY');
		$aInput = $this->aInput['input'][$type];
		
		$sGroupBy = empty($aInput['groupBy'])?'':$aInput['groupBy'];
		
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		
		foreach($aInput['tables'] as $table => $aTable){
			
			$aConftion=$this->getCondition($type,$table);
			$aConftionAry = $this->getCondition($type,$table, 'array');
			$aConf[$table]=array(
				'noRecord' => 0,
				'limit' => 0,
				'dimen_array' =>$this->getDimenFieldArray($type,$table),
				'metric_array' =>$this->getMetricFieldArray($type,$table),
				'field_array' =>$this->getField($type,$table, 'array'),
				'table' => $oDict->ALYStableDefine($table),
				'condition' =>$aConftion,
				'condition_array' =>$aConftionAry,
			);
			
			//if($sGroupBy)$aConf[$table]['groupby']=$sGroupBy;
			
		}
		//print_r($aConf);
		return $aConf;
	}
	
	

}
?>