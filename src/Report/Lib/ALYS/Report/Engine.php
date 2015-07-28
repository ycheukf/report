<?php
namespace YcheukfReport\Lib\ALYS\Report;

class Engine extends \YcheukfReport\Lib\ALYS\Report{
	public $aInput;
	public $aOutput;
	public $model;




	public function __construct(){
		$this->aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$this->aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();

		foreach($this->aInput['input'] as $modelType=>$v){
			if(count($v)>0)
				$this->$modelType = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.model.{$modelType}");
		}
	}


	/**
	* get date param
	*/
	public function ALYSgetDateField(){
		return 'date';
	}

	/**
	* get date param
	*/
	public function getDate($type){
		$aDates=array();
		$table=$this->aInput['input'][$type]['mainTable'];
		$dateField = \YcheukfReport\Lib\ALYS\Report::getDateFeildByTable($type,$table);
		if(!empty($this->aInput['date'])&&is_array($this->aInput['date'])){
			foreach($this->aInput['date'] as $aDate){
				$aDates[]="(".$dateField." >= '".$aDate['s']."' and ".$dateField." <= '".$aDate['e']."')";
			}
		}
		return $aDates;
	}

	/**
	* get date param
	*/
	public function getDateNew($type){
		$table2Field=$this->aInput['input'][$type]['mainTable']['table2Field'];
		$aDates=array();
		if(!empty($this->aInput['date'])&&is_array($this->aInput['date'])){
			foreach($this->aInput['date'] as $k=>$aDate){
				foreach($table2Field as $table=>$dateField){
					$aDates[$k][$table]="(".$dateField." >= '".$aDate['s']."' and ".$dateField." <= '".$aDate['e']."')";
				}
			}
		}
		return $aDates;
	}


	/**
	* get condition param
	*/
	public function getCondition($type,$table, $returntype='string'){
		$aReturn=$aReturn2=array();

		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aTable=$aInput['input'][$type]['tables'][$table];
		if(isset($aTable['filter']) && is_array($aTable['filter'])){
			foreach($aTable['filter'] as $condition){
				$aReturn[]=$condition['key'].' '.$condition['op'].' '.$condition['value'];
				$aReturn2[] = $condition;
			}
		}
		if(isset($aInput['nodateFlag']) && !$aInput['nodateFlag'] && count($aInput['date'])){
			foreach($aInput['date'] as $row){
				$aReturn2[] = array(
					'key' => \YcheukfReport\Lib\ALYS\ALYSConfig::get('dateField'),
					'op' => '>=',
					'value' => $row['s'],
				);
				$aReturn2[] = array(
					'key' => \YcheukfReport\Lib\ALYS\ALYSConfig::get('dateField'),
					'op' => '<=',
					'value' => $row['e'],
				);
			}
		}
//		var_export($aReturn2);
		return $returntype=='string' ? $aReturn : $aReturn2;
	}




	/**
	* get field param
	*/
	public function getField($type,$table, $returntype='string'){
		$aTable=$this->aInput['input'][$type]['tables'][$table];
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		$sField='';
		$aSqlField=array();
//		var_dump($aTable);
		if(is_array($aTable['metric'])){
			foreach($aTable['metric'] as $field){
				$aSqlField[]=$oDict->ALYSmetric2Field($field);
			}
		}
//		var_dump($aSqlField);
		if(is_array($aTable['dimen']['field'])&&!empty($aTable['dimen']['field'])){
			$aSqlField = array_merge($aSqlField, $aTable['dimen']['field']);
		}
		if(!empty($aTable['dimen']['concatKey']))
			$aSqlField[] = $aTable['dimen']['concatKey'];
		return ($returntype=='string') ? implode(' , ',$aSqlField) : $aSqlField;
	}


	/**
	* get field param
	*/
	public function getDimenFieldArray($type,$table){
		$aTable=$this->aInput['input'][$type]['tables'][$table];
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		$sField='';
		$aReturn = $aTable['dimen']['dimenkey2field'];
		return $aReturn;
	}

	/**
	* get field param
	*/
	public function getMetricFieldArray($type,$table){
		$aTable=$this->aInput['input'][$type]['tables'][$table];
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		$aReturn = array();
		if(is_array($aTable['metric'])){
			foreach($aTable['metric'] as $field){
				$aTmp = $oDict->ALYSmetric2Field($field);
				if(is_string($aTmp)){
					list($sValue, $sLabel) = explode(" as ", $oDict->ALYSmetric2Field($field));
					$aReturn[trim($sLabel)] = trim($sValue);
				}else{
					$aReturn = array_merge($aReturn, $aTmp);
				}
			}
		}
		return $aReturn;
	}


	/**
	* ����ַ���ϵ����
	*/
	public function _sortArray($allField){
//		var_export($allField);
		$allFieldTmp=$allField;
		$iField=count($allField);
		for($i=0;$i<$iField;$i++){
			for($j=$i+1;$j<$iField;$j++){
				if(stristr($allFieldTmp[$j],$allFieldTmp[$i])){
					$tmp=$allFieldTmp[$i];
					$allFieldTmp[$i]=$allFieldTmp[$j];
					$allFieldTmp[$j]=$tmp;
				}
			}
		}
//		var_export($allField);
		return $allFieldTmp;
	}

	/**
	* sql
	*/
	public function _executeSqlData($type, $aDatas){

		$aDataNs=array();
		$aTables=$this->aInput['input'][$type]['table'];
		$aMainTable=$this->aInput['input'][$type]['mainTable'];
		$aComField=$aMainTable['field3'];

		$allField=array();
		$aReplace=array('+' ,'-' ,'*' ,'/' ,'(' ,')');
		$sAllField='';
		//print_r($aMainTable['showField']);

		//��ȡ���е���ʾ�ֶ��Լ��乫ʽ
		$aTmp = array();
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		foreach($aMainTable['showField'] as $showField){
			$sEval=$oDict->ALYSmetricExpresion($showField);
			$aTmp[] = str_replace($aReplace,',',$sEval);
			$sAllField .=str_replace($aReplace,',',$sEval).',';
		}
//		var_export($aTmp);
		$sAllField = join(",", $aTmp);
//		$sAllField=preg_replace('/,+/',',',$sAllField);
		//echo $sAllField;
//		$sAllField=substr($sAllField,0,-1);
//		echo $sAllField;
		$allField=array_unique(explode(',',$sAllField));
		$allField = array_filter($allField);
		//var_export($allField);
		$allFieldTmp=$this->_sortArray($allField);

//		print_r($aDatas);

		$aTable2Field=$aMainTable['field2Table'];

		//var_dump($aTable2Field);
//		echo "aDatas=";print_r($aDatas);

		if(is_array($aDatas)){
			foreach($aDatas as $date_i=> $aData){
				if(is_array($aData[$aMainTable['table']])){
					foreach($aData[$aMainTable['table']] as $k=> $Data){
						$aDataNs[$date_i][$k]['concatKey']=$Data['concatKey'];
						foreach($allField as $field){
							$aDataNs[$date_i][$k][$field]=$aData[$aTable2Field[$field]][$k][$field];
						}
					}
				}
			}
		}
		//print_r($aDataNs);
		$aDataNew=array();
		$i_1=0;
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		foreach($aDataNs as $date_i =>$aDataN){
			foreach($aDataN as $k=>$Data){

				$aDataNew[$i_1][$k]['concatKey']=$Data['concatKey'];
				foreach($aMainTable['showField'] as $showField){
					if(in_array($showField,$aComField)){
						$sEval = $oDict->ALYSmetricExpresion($showField);
						foreach($allFieldTmp as $field){
							$sEval=str_replace($field,$Data[$field],$sEval);

						}
						if(!$sEval) $sEval=0;
						@eval('$val='.$sEval.';');

					}else{
						$val=$Data[$showField];
					}
					if(!$val) $val=0;
					$aDataNew[$i_1][$k][$showField]=$val;
				}

			}
			$i_1++;
		}
		$aData=$aDataNew;

//		print_r($aDataNew);
		//print_r($aTable2Field);



		return $aData;
	}

	//��ȡ���
	public function getAdvancedData($type){
		$aInput = $this->aInput['input'][$type];
		$data = array();
		$advancetype = $aInput['advanced']['type'];

		//ֻ֧�����ָ�ʽ sql��ѯ����ݼ�����
		if('datas'==$advancetype){
			if(!empty($aInput['advanced']['data']))$data = $aInput['advanced']['data'];
		}else{
			return $data;
		}

		$return_data = $this->processAdvanceData($data,$type);
		return $return_data;
	}

	//������� ����concatKey �ֶ�
	public function processAdvanceData($data,$type){
		if(!is_array($data)||empty($data))return array();

		$aDimens = \YcheukfReport\Lib\ALYS\Report\Advance::getAdvanceDimens($type);

		$return_data = array();
		$aInput = $this->aInput['input'][$type];
		foreach($data as $dk => $dv){
			if(is_array($dv)){
				foreach($dv as $k => $v){
					$tmp = array();
					foreach($aDimens as $dimen){
						$tmp[$dimen] = @$v[$dimen];
					}
					$v['concatKey'] = implode($this->splitChar,$tmp);
					$return_data[$dk][$aInput['mainTable']['table']][$k]=$v;
				}
			}
		}

		return $return_data;
	}


}
?>