<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Input extends \YcheukfReport\Lib\ALYS\Report{

	public $aInput;


	public function __construct(){
		$this->aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();


	}
	public function chkInputParam($type){

	}

	/**
	* check private input param
	*/
	public function _chkInputParam($type){
		$aTables=$this->aInput['input'][$type];
		$aDimenKey=array('key', 'value', 'group','options','selected', 'sortAble','dimenFilter','timeslotType', 'thclass');
		$aMetricKey=array('key', 'value', 'show','type','trendTypeStyle','ispercent');
		if(isset($aTables['table']) && is_array($aTables['table'])){
			foreach($aTables['table'] as $aTable){

				if(!empty($aTable['dimen'])&&is_array($aTable['dimen'])){
					foreach($aTable['dimen'] as $dimen){
						$DimenKey=array_keys($dimen);
						foreach($DimenKey as $key){
							if(!in_array($key,$aDimenKey)){
								throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','dimen->'.$key);
							}
							// if(!preg_match($dateReg,$Date[$key])){
								// throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_VALUE_WRONG','date');
							// }
						}

					}
				}

				if(!empty($aTable['metric'])&&is_array($aTable['metric'])){
					foreach($aTable['metric'] as $metric){
						$MetricKey=array_keys($metric);
						foreach($MetricKey as $key){
							if(!in_array($key,$aMetricKey)){
								throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','metric->'.$key);
							}
						}

					}
				}
			}
		}

		$this->chkInputParam($type); //����
	}


	/**
	* flash X
	*/
	public function getFlashGroupBy(){
		$sGroupBy='day';
		if(isset($this->aInput['input']['flash']['table']) && is_array($this->aInput['input']['flash']['table'])){
			foreach($this->aInput['input']['flash']['table'] as $t=>$aTmp){
				if(count($aTmp['dimen'])){
					foreach($aTmp['dimen'] as $aTmp2){
						//if($aTmp2['key'] != 'timeslot')continue;
						if($aTmp2['group']) $sGroupBy = $aTmp2['selected'];
					}
				}
			}
		}
		return $sGroupBy;
	}

	/**
	* ׼���������
	*/
	public function initInput($type){

		$this->_chkInputParam($type);

		if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced($type)){
			$this -> initInputAdvance($type);
//			return;
		}

		$aTmpTables=array();
		$aTables=$this->aInput['input'][$type]['table'];
		$aMainTable['showField']=array();
		$aMainTable['field3']=array();
		$aMainTable['table2Field']=array(); //ֻ���date,day
		//echo "$type=";echo "aTables=";print_r($aTables);

		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		foreach($aTables as $table => $aTable){
			if(!isset($aMainTable['table'])) $aMainTable['table']=$table;
			$aDimenField=array();
			if(!empty($aTable['dimen'])&&is_array($aTable['dimen'])){
				foreach($aTable['dimen'] as $dimen){
					$key=$dimen['key'];
					if(isset($aTmpTables[$table]['dimen']['key'])&&in_array($key,$aTmpTables[$table]['dimen']['key']))continue;//dimenȥ��
					if($dimen['group']){

						$interval = $dimen['selected'];

						$sField=$oDict->ALYSdimen2Field($key, $interval,$type,$table);
						$aTmpTables[$table]['dimen']['key'][]=$key;
						$aTmpTables[$table]['dimen']['dimenkey2selected'][$key] = $interval;
						$aTmpTables[$table]['dimen']['dimenkey2field'][$key] = $sField;
						$aTmpTables[$table]['dimen']['field'][]=$sField . " as " .$key;
						$aDimenField['key'][]=$sField;
						if(empty($aMainTable['table2Field'][$table])){
							$aMainTable['table2Field'][$table]=\YcheukfReport\Lib\ALYS\Report::getDateFeildByTable($type,$table);
						}

						if($type != 'flash'){//Ϊflashʱ����ҪshowField
							if(!in_array($key,$aMainTable['showField'])){
								$aMainTable['showField'][]=$key; //��ʾ���ֶ� ά��
								$aMainTable['field2Table'][$key]=$table;
							}
						}
					}else{
						$interval = $dimen['selected'];
						$sField=$oDict->ALYSdimen2Field($key, $interval,$type,$table);
						$aTmpTables[$table]['dimen']['field'][]=$sField . " as " .$key;
					}
					foreach($this->aInput['filters'] as $aFilter){
						if(in_array($aFilter['key'],$dimen['options'])){
							$aTmpTables[$table]['filter'][]=$aFilter;
						}
					}
				}
			}
//var_export($aTmpTables);
			$tmpAry = array();
			if(is_array($aDimenField['key'])){
				foreach($aDimenField['key'] as $field){
					$tmpAry[] = "CAST(IF(".$field."<=>null, 0, ".$field.") AS CHAR)";
				}
				$aTmpTables[$table]['dimen']['concatKey']= "CONCAT_WS('".$this->splitChar."', ".implode(',',$tmpAry).") as concatKey";
			}

			$aTmpTables[$table]['metric']=array();
			if(!empty($aTable['metric'])&&is_array($aTable['metric'])){
				foreach($aTable['metric'] as $metric){
					if($metric['show']){
						//if($aMainTable['table']==$table)
						if(!in_array($metric['key'],$aMainTable['showField'])){
							$aMainTable['showField'][]=$metric['key']; //��ʾ���ֶ� ָ��
						}
						if(3==$metric['type'][0]){
							//ȥ��
							if(!in_array($metric['key'],$aMainTable['showField'])){
								$aMainTable['showField'][]=$metric['key']; //��ʾ���ֶ� ָ��
							}
						}else{
							if(!in_array($metric['key'],$aMainTable['showField']))
								$aMainTable['showField'][]=$metric['key'];
						}

//		var_export($aMainTable['showField']);
						switch($metric['type'][0]){

							case 1:

							case 2:
								//if(!in_array($metric['key'],$aTmpTables[$table]['metric'])){
									$aTmpTables[$table]['metric'][]=$metric['key'];
								//}
								$aMainTable['field2Table'][$metric['key']]=$table;
								break;
							case 3:
								if(!in_array($metric['key'],$aMainTable['field3'])){
									$aMainTable['field3'][]=$metric['key']; //��ʾ���ֶ� ָ��
								}

								if($table==$aMainTable['table']){//�������
									$key=$metric['type'][1][0];
									if(!in_array($key,$aTmpTables[$table]['metric'])){
										$aTmpTables[$table]['metric'][]=$key;
									}
									$aMainTable['field2Table'][$key]=$table;
								}else{//�ӱ����
									$key=$metric['type'][1][0];
									if(!in_array($key,$aTmpTables[$table]['metric'])){
										$aTmpTables[$table]['metric'][]=$key;
									}
									$aMainTable['field2Table'][$key]=$table;
								}
								break;

						}
					}


				}
			}
		}
		$this->aInput['input'][$type]['groupby']=$this->getFlashGroupBy();
		$this->aInput['input'][$type]['mainTable']=$aMainTable;
		$this->aInput['input'][$type]['tables']=$aTmpTables;
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}

	private function _initDimes($aDimens){
		foreach($aDimens as $k=>$aDimen)
			$aDimens[$k] = is_string($aDimen) ? array('key'=>$aDimen) : $aDimen;
		foreach($aDimens as $k=>$aDimen){
			if(!(isset($aDimen['group']) and $aDimen['group']===false)){
				$aDimens[$k]['group']=true;
			}

			if(!(isset($aDimen['selected']))){
				if(isset($aDimen['options'])){
					$aDimens[$k]['selected']=$aDimen['options'][0];
				}else{
					$aDimens[$k]['selected']=$aDimen['key'];
				}
			}
			if(!(isset($aDimen['options']))){
				$aDimens[$k]['options']=array($aDimens[$k]['selected'],'none');
			}
			if(!(isset($aDimen['timeslotType']))){
				$aDimens[$k]['timeslotType'] = 0;
			}
		}
		return $aDimens;
	}
	private function _initMetrics($aMetrics){
		foreach($aMetrics as $k=>$aTmp)
			$aMetrics[$k] = is_string($aTmp) ? array('key'=>$aTmp) : $aTmp;
		foreach($aMetrics as $k=>$aMetric){
			if(!(isset($aMetric['show']) and $aMetric['show']===false)){
				$aMetrics[$k]['show']=true;
			}
			if(!(isset($aMetric['type']))){
				$aMetrics[$k]['type']=array(1);
			}
		}
		return $aMetrics;
	}

	/**
	* ��ʼ���������
	* ��ȫ
	*/
	public function _initaInput($type){

//		print_r($type);
		if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced($type)){
			$this -> _initaInputAdvance($type);
			$aInput=  $this->aInput['input'][$type]['advanced'];
			if(!empty($aInput['dimen'])&&is_array($aInput['dimen'])){
				$aInput['dimen'] = $this->_initDimes($aInput['dimen']);
			}

			if(!empty($aInput['metric'])&&is_array($aInput['metric'])){
				$aInput['metric'] = $this->_initMetrics($aInput['metric']);
			}
			$this->aInput['input'][$type]['advanced'] = $aInput;
		}else{
			$aInput=  $this->aInput['input'][$type]['table'];
			foreach($aInput as $table=> $aTable){
				if(!empty($aTable['dimen'])&&is_array($aTable['dimen'])){
					$aInput[$table]['dimen'] = $this->_initDimes($aTable['dimen']);
				}

				if(!empty($aTable['metric'])&&is_array($aTable['metric'])){
					$aInput[$table]['metric'] = $this->_initMetrics($aTable['metric']);
				}
			}
			$this->aInput['input'][$type]['table'] = $aInput;
		}

		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}

	//��֯ Ϊ�˸�engine��
	public function initInputAdvance($type){
		$aMainTable = array();
		$tables = array();
		//$aMainTable ��֯
		$table = 'mainTable';

		$aMetric = \YcheukfReport\Lib\ALYS\Report\Advance::getAdvanceMetrics($type);
		if('detail'==$type){
			$aDimens = \YcheukfReport\Lib\ALYS\Report\Advance::getAdvanceDimens($type);
			$aMainTable['showField'] = array_merge($aDimens,$aMetric);
		}else{
			$aMainTable['showField'] = $aMetric;
		}

		if(empty($aMainTable['showField'])){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','need metrics');
		}
		$aMainTable['field3']=array();
		$aMainTable['table2Field']=array();
		$aMainTable['table'] = $table;//ֻ����һ������
		if(is_array($aMainTable['showField'])){
			foreach($aMainTable['showField'] as $f){
				$aMainTable['field2Table'][$f] = $table;
			}
		}

		//tables ��֯
		$aDimens = \YcheukfReport\Lib\ALYS\Report\Advance::getAdvanceDimens($type);
		$tables[$table]['dimen']['key'] = $aDimens;
		$tables[$table]['dimen']['dimenkey2field'] = array_flip($aDimens);
		$this->aInput['input'][$type]['mainTable']=$aMainTable;
		$this->aInput['input'][$type]['tables'] = $tables;
		//��ʼά�� ָ�� (����)
		$this->aInput['input'][$type]['table'][$table]['dimen']=$this->aInput['input'][$type]['advanced']['dimen'];
		$this->aInput['input'][$type]['table'][$table]['metric']=$this->aInput['input'][$type]['advanced']['metric'];
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}

	public function _initaInputAdvance($type){

	}


}
?>