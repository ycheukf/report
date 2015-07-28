<?php
namespace YcheukfReport\Lib\ALYS\Report\Engine;
class Detail extends \YcheukfReport\Lib\ALYS\Report\Engine{

	protected $_type = 'detail';

	public function __construct(){
		parent::__construct();

	}

	protected function _init(){

	}

	/**
	* 获得数据入口
	*/
	public function getData(){
		$type='detail';

		if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced($type)){

			$aData = $this -> getAdvancedData($type);
//			var_dump($aData);
		}else{
			$this -> _init();//初始化

			$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
			$aConf=$this->getConf($type);
			$aDatesNew=$this->getDateNew($type);
			$aDates=$this->getDate($type);
//			print_r($aConf);
			$aConfs=array();
			$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');

//			var_dump($aInput['nodateFlag']);
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
//			var_dump($aCondition);
			foreach($aConfs as $k =>$aConf){
				foreach($aConf as $table=>$Conf){
					list($aData[$k][$table], $nTotal) = $this->$type->getAlldata($Conf); //主表数据
					$this->_setTotal($type, $nTotal);
					break;
				}
			}
	//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aData, 'a', 'aData');
			$aInput = $this->aInput['input'][$type];
			$mainTable=$aInput['mainTable']['table'];
			$table2Field=$aInput['mainTable']['table2Field'];


			//echo "dimens=";print_r($dimens);
			//$aGroup=$aInput['tables'][$mainTable]['dimen']['key'];
			//$aGroup=$aInput['tables'][$mainTable]['dimen']['dimenkey2field'];
			$aConcatKey=array(); //次表条件
			foreach($aConfs as $k =>$aConfTmp){
				if(!empty($aData[$k][$mainTable])){
					foreach($aData[$k][$mainTable] as $j=>$Data){
						foreach($aInput['tables'] as $table => $aTable){
							if($table <> $mainTable){
								$dimens=$aInput['tables'][$table]['dimen'];
								foreach($dimens['dimenkey2field'] as $keyTmp=>$fieldTmp){
									$aConcatKey[$k][$fieldTmp][$j]=$Data[$keyTmp];
								}
							}
						}
					}
				}else{//若主表无数据, 则将此表条件制空
					foreach($aInput['tables'] as $table => $aTable){
						if($table <> $mainTable){
							$dimens=$aInput['tables'][$table]['dimen'];
							foreach($dimens['dimenkey2field'] as $keyTmp=>$fieldTmp){
								$aConcatKey[$k][$fieldTmp][$j] = '';
							}
						}
					}

				}
			}
	//		echo "aConcatKey=";print_r($aConcatKey);
			$aConfSecond=array();
			foreach($aInput['tables'] as $table => $aTable){ //次表
				if($table <> $mainTable){
					$aConftion=array();
					$aConftion = $this->getCondition($type,$table);
					$aConftionAry = $this->getCondition($type,$table, 'array');
					$aConfSecond[$table]=array(
						'noRecord' => 0,
						'limit' => 0,
						'field_array' =>$this->getField($type,$table, 'array'),
						'dimen_array' =>$this->getDimenFieldArray($type,$table),
						'metric_array' =>$this->getMetricFieldArray($type,$table),
						'groupby' =>$aInput['tables'][$table]['dimen']['key'],
						'table' => $oDict->ALYStableDefine($table),
						'condition' =>$aConftion,
						'condition_array' =>$aConftionAry,
					);
				}
			}
			$aConfSeconds=array();
			foreach($aDates as $k=>$Date){
				foreach($aConfSecond as $table =>$Conf){
					$aConfSeconds[$k][$table]=$Conf;

					$aCondition=$Conf['condition'];
					$aCondition=array_merge(array($aDatesNew[$k][$table]),$aCondition);

					if(is_array($aConcatKey[$k])){
						foreach($aConcatKey[$k] as $group =>$val){
							$aTmp=array();
							$val=array_unique($val);
							$quotes=$oDict->ALYSgetDimenTypeOf($group);
	//		var_export($group);
	//		var_export($quotes);
	//		var_export($val);
	//echo "<hr>";
							foreach($val as $v)  $aTmp[]=$quotes.$v.$quotes;
							$aCondition[]=$group .' in ('.implode(',',$aTmp).')';
						}
					}

					$aConfSeconds[$k][$table]['condition']=$aCondition;
				}
			}

			foreach($aConfSeconds as $k =>$aConf){
				foreach($aConf as $table=>$Conf){
					list($aData[$k][$table], $nTmpTotal) = $this->$type->getAlldata($Conf); //次表数据
				}
			}
		}
//		echo "<xmp>";
//		echo "aConfSeconds=";print_r($aConfSeconds);
		$aData=$this->_executeSqlData($type,$aData);
//		echo "<xmp>";
		return $aData;

	}

	//设置数据总数
	protected function _setTotal($type, $nTotal){
		$this->aInput['input'][$type]['page']['total']=$nTotal; //总条数
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
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
// SELECT SQL_CALC_FOUND_ROWS domainId as domainId, CONCAT_WS('__split__', IF(domainId<=>null, 0, domainId)) as concatKey, sum(pageView) as pageView, sum(visitor) as
// visitor, sum(sumDepth)/sum(sumSession) as avgDepth, sum(sumTimeLength)/sum(sumSession) as avgTimeLength, sum(sumSession) as sumSession, sum(newPageView) as newPageView,
// sum(newVisitor) as newVisitor, sum(uniqueIP) as uniqueIP FROM StatsPageviewAll WHERE (((date >= '2011-11-10' and date <= '2011-11-14')) and advertiserId in (84) and
// domainId in (-1, 1418, 1419, 1420, 1422, 1437, 1441, 1446, 1447, 1450, 1453, 1460, 1464, 1470, 1475, 1489, 1503, 1510, 1513, 1519, 1542, 1543, 1546, 1588, 1608, 1611,
// 1614, 1619, 1632, 1633, 1669, 1674, 1719, 1755, 1788, 1822, 1835, 1916, 1962, 2009, 2089, 2094, 2134, 2152, 2154, 2156, 2198, 2204, 2283, 2284, 2401, 2411, 2505, 2530,
// 2543, 2553, 2564, 2621, 2623, 2624, 2632, 2668, 2688, 2715, 2722, 2734, 2748, 2751, 2790, 2792, 2827, 2963, 3008, 3120, 3134, 3191, 3195, 3199, 3207, 3270, 3271, 3297,
// 3307, 3310, 3349, 3357, 3364, 3390, 3480, 3485, 3493, 3528, 3593, 3453, 3478, 3494, 3544, 3547, 3564, 3610)) GROUP BY domainId ORDER BY pageView desc LIMIT 0, 10
	public function getConf($type){
		$aConf=array();
		if(!count($this->aInput['input'][$type]['table']))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_TABLE_EMPTY');
//		$aInput = $this->aInput['input'][$type];

		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');

		//print_r($this->aInput['input'][$type]);
		$this->aInput['input'][$type]['page']['current_page'] = empty($this->aInput['input'][$type]['page']['current_page'])?0:(int)$this->aInput['input'][$type]['page']['current_page'];
		$this->aInput['input'][$type]['page']['items_per_page'] = empty($this->aInput['input'][$type]['page']['items_per_page'])?10:(int)$this->aInput['input'][$type]['page']['items_per_page'];
		$start=$this->aInput['input'][$type]['page']['current_page']*$this->aInput['input'][$type]['page']['items_per_page'];

		$is_limit = isset($this->aInput['input'][$type]['page']['is_limit'])?($this->aInput['input'][$type]['page']['is_limit']===0?0:1):1;

		foreach($this->aInput['input'][$type]['tables'] as $table => $aTable){

			$aConftion=$this->getCondition($type,$table);
			$aConftionAry = $this->getCondition($type,$table, 'array');
			$aConf[$table]=array(
				'noRecord' => 0,
				'limit' => $is_limit,
				'start' => $start,
				'length' => $this->aInput['input'][$type]['page']['items_per_page'],
				'dimen_array' =>$this->getDimenFieldArray($type,$table),
				'metric_array' =>$this->getMetricFieldArray($type,$table),
				'field_array' =>$this->getField($type,$table, 'array'),

				'groupby' => $aTable['dimen']['key'],
				'table' => $oDict->ALYStableDefine($table),
				'condition' =>$aConftion,
				'condition_array' =>$aConftionAry,
			);
			if(isset($this->aInput['input'][$type]['orderby']))
				$aConf[$table]['orderby'] = $this->aInput['input'][$type]['orderby'];
			break; //只配置主表
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
		//print_r($aConf);
		return $aConf;
	}

	/**
	* 处理sql结果数据
	*/
	public function _executeSqlData($type, $aDatas){
//var_dump($aDatas);
		$aDataNs=array();
		$aTables=$this->aInput['input'][$type]['tables'];
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		$sMainTableName = $aMainTable['table'];//主表名

		//所有field 包括show为false的
		$arrFieldShowFalse = array();
		$arrField2TableShowFalse = array();
		if(isset($this->aInput['input'][$type]['table'][$sMainTableName]['metric']))
		{
			$arrFields = $this->aInput['input'][$type]['table'][$sMainTableName]['metric'];
			foreach($arrFields as $fv)
			{
				if(isset($fv['show'])&&false==$fv['show'])
				{
					$arrFieldShowFalse[] = $fv['key'];
					$arrField2TableShowFalse[$fv['key']] = $sMainTableName;
				}
			}
		}

		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
//		var_export($aTables[$aMainTable['table']]);
		$allField=array();
		$aReplace=array('+' ,'-' ,'*' ,'/' ,'(' ,')');
		$sAllField='';
//		print_r($aMainTable['showField']);
		foreach($aMainTable['showField'] as $showField){
			if(in_array($showField,$aMainTable['field3'])){
				$sEval=$oDict->ALYSmetricExpresion($showField);
				$sEval=preg_replace('/\s+/','',$sEval);
				$sAllField .=str_replace($aReplace,',',$sEval).',';
			}else{
				$allField[]=$showField;
			}

		}

		$sAllField=preg_replace('/,+/',',',$sAllField);

		$sAllField=substr($sAllField,0,-1);
		if($sAllField)$allField=array_unique(array_merge($allField,explode(',',$sAllField)));
		$allField=array_unique(array_merge($allField,$arrFieldShowFalse));
		$allFieldTmp=$this->_sortArray($allField);
//		var_export($allFieldTmp);

		//找到拼接数组的gap
		$aExtraDimen = array();
		foreach($aTables as $table => $aTmp){
			if($table == $aMainTable['table'])continue;
			foreach($aTables[$aMainTable['table']]['dimen']['key'] as $i => $sDimen){
				if(!in_array($sDimen, $aTables[$table]['dimen']['key'])){
					$aExtraDimen[$table][$sDimen] = $i;
				}
			}
		}

		$aTable2Field=$aMainTable['field2Table'];
		if(is_array($arrField2TableShowFalse))$aTable2Field=$aTable2Field+$arrField2TableShowFalse;

		//print_r($aMainTable['showField']);
		//$allField=array('sp_ns');
//		print_r($aDatas);
		//print_r($aTable2Field);
		//echo "aDatas=";print_r($aDatas);
		$aConcatKey2ConcatKey = array();//拼接数组的映射数组
		$aDataNsKey=array();
//		var_export($aMainTable['table']);
//		var_export($aDatas);
		foreach($aDatas as $date_i=> $aData){
			foreach($aTables as $table=>$v){
				foreach($aData[$table] as $Data){
					if($table==$aMainTable['table']){
						//var_dump($Data['concatKey']);
						$aConcatKey2ConcatKey[$table][$Data['concatKey']] = $Data['concatKey'];
					}else{
						foreach($aConcatKey2ConcatKey[$aMainTable['table']] as $vTmp){
//							$this->splitChar
							$aSplit = explode($this->splitChar, $vTmp);
							if(isset($aExtraDimen[$table])){
								foreach($aExtraDimen[$table] as $k2){
									unset($aSplit[$k2]);
								}
							}
							$vTmp2 = join($this->splitChar, $aSplit);
							$aConcatKey2ConcatKey[$table][$vTmp] = $vTmp2;
						}
					}
					$aDataNsKey[$date_i][$table][$Data['concatKey']]=$Data;
				}
			}
		}
//		print_r($aTables[$aMainTable['table']]['dimen']['dimenkey2selected']);
//		print_r($aDataNsKey);
//		print_r($aTable2Field);
		foreach($aDataNsKey as $date_i=> $aData){
			if(is_array($aData[$aMainTable['table']])){
				foreach($aData[$aMainTable['table']] as $k=> $Data){
					//$aDataNs[$date_i][$k]['concatKey']=$Data['concatKey'];
					$concatKey=$Data['concatKey'];
					foreach($allField as $field){
						if(empty($field))continue;
						$sTmpTable = $aTable2Field[$field];
						$dataConcatKey = $aConcatKey2ConcatKey[$sTmpTable][$concatKey];
						$sSelectedField = isset($aTables[$aMainTable['table']]['dimen']['dimenkey2selected'][$field]) ? $aTables[$aMainTable['table']]['dimen']['dimenkey2selected'][$field] : $field;
						$aDataNs[$date_i][$concatKey][$field] = isset($aData[$sTmpTable][$dataConcatKey][$sSelectedField]) ? $aData[$sTmpTable][$dataConcatKey][$sSelectedField] : (isset($aData[$sTmpTable][$dataConcatKey][$field]) ? $aData[$sTmpTable][$dataConcatKey][$field] : 0);
					}
				}
			}
		}
		//print_r($aMainTable['field3']);
//var_dump($aDataNs);
		//print_r($allField);
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		foreach($aDataNs as $date_i =>$aDataN){
			foreach($aDataN as $key=>$Data){
				foreach($aMainTable['field3'] as $field3){
					$sEval = $oDict->ALYSmetricExpresion($field3);
					//echo "sEval=".$sEval;
					foreach($allFieldTmp as $field){
						if(empty($Data[$field])) $Data[$field]=0;
						$sEval=str_replace($field,$Data[$field],$sEval);
					}
					//echo "sEval=".$sEval;
					@eval('$val='.$sEval.';');
					if(!$val) $val=0;
					$aDataNs[$date_i][$key][$field3]=$val;
				}
			}
		}
		$aData=$aDataNs;
//var_dump($aData);
		return $aData;
	}
}
?>