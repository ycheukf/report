<?php
namespace YcheukfReport\Lib\ALYS\Report\Engine;

class Flash extends \YcheukfReport\Lib\ALYS\Report\Engine{

	public function __construct(){
		parent::__construct();
		
	}
	/**
	* 获得数据入口
	*/
	public function getData(){
		$type='flash';
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		
		if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced($type)){
			
			$aData = $this -> getAdvancedData($type);
		}else{
			$aConf=$this->getConf($type);
			$aDatesNew=$this->getDateNew($type);
			$aDates=$this->getDate($type);
			$aConfs=array();
			
//			print_r($aConf);
			if(is_array($aDates)){
				foreach($aDates as $k=>$Date){
					foreach($aConf as $table =>$Conf){
						$aConfs[$k][$table]=$Conf;
						
						$aCondition=$Conf['condition'];
						if(isset($aInput['nodateFlag']) && !$aInput['nodateFlag']){//have date
							$aCondition=array_merge(array($aDatesNew[$k][$table]),$aCondition);
						}
//						$aCondition=array_merge(array($aDatesNew[$k][$table]),$aCondition);
						
						$aConfs[$k][$table]['condition']=$aCondition;
					}
				}
			}
			//\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aConfs, 'a', 'aConfs');
			if(is_array($aConfs)){
				foreach($aConfs as $k =>$aConf){
					foreach($aConf as $table=>$Conf){
						list($aData[$k][$table], $nTmpTotal) = $this->$type->getAlldata($Conf);
					}
				}
			}
		}
		
		
		
		
		$aData = $this->_executeSqlData($type,$aData);
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
//	SELECT SQL_CALC_FOUND_ROWS date as timeslot, CONCAT_WS('__split__', IF(date<=>null, 0, date)) as concatKey, sum(pageView) as pageView FROM StatsPageviewAll WHERE (((date
// >= '2010-03-02' and date <= '2010-03-25')) and advertiserId in (1) and domainId in (-1, 2, 4, 5, 16, 17, 20, 24, 30, 33, 39, 43, 44, 45, 48, 50, 53, 55, 57)) GROUP BY
// timeslot ORDER BY timeslot ASC
	public function getConf($type){
		$aConf=array();
		if(!count($this->aInput['input'][$type]['table']))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_TABLE_EMPTY');
		$aInput = $this->aInput['input'][$type];
		
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		
		//查找的order limit设置
		$aDataConf = array();
		if(!empty($aInput['dataConf']['orderBy'])){
			$aDataConf['orderby'] = $aInput['dataConf']['orderBy'];
		}
		if(!empty($aInput['dataConf']['length'])){
			$aDataConf['limit'] = 1;
			$aDataConf['start'] = empty($aInput['dataConf']['start'])?0:(int)$aInput['dataConf']['start'];
			$aDataConf['length'] = (int)$aInput['dataConf']['length'];
		}
		
		if(is_array($aInput['tables'])){
			foreach($aInput['tables'] as $table => $aTable){
				
				$aConftion=$this->getCondition($type,$table);
				$aConftionAry = $this->getCondition($type,$table, 'array');
				//$aConftion=array_merge(array($sDate),$aConftion);
				//echo $table;
				//print_r($aConftion);
				$aConf[$table]=array(
					'noRecord' => 0,
					'limit' => 0,
					'dimen_array' =>$this->getDimenFieldArray($type,$table),
					'metric_array' =>$this->getMetricFieldArray($type,$table),
					'field_array' =>$this->getField($type,$table, 'array'),
					'groupby' => $aTable['dimen']['key'],
					'table' => $oDict->ALYStableDefine($table),
					'condition' =>$aConftion,
					'condition_array' =>$aConftionAry,
				);
				$aConf[$table] = array_merge($aConf[$table],$aDataConf);
			}
		}
		//print_r($aConf);
		return $aConf;
	}
	
	
	
	
}
?>