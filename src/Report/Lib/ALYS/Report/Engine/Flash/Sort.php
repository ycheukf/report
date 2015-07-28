<?php
namespace YcheukfReport\Lib\ALYS\Report\Engine\Flash;
class Sort extends Flash{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	* 重写
	* 获得数据入口
	*/
	public function getData(){
		$type_R='detail';
		$nIndex=0;
		$aInput = $this->aInput['input'][$type_R];
		$mainTable=$aInput['mainTable']['table'];
		
		$dimens=$aInput['tables'][$mainTable]['dimen'];

		//获取列表数据
		$aData_R=array();
		$aData_R=$this->aInput['internal']['listData'][$nIndex];
		$aConcatKey=array(); //次表条件
		if(count($aData_R)>0){
			foreach($aData_R as $j=>$aDataTmp){
				foreach($dimens['key'] as $groupField){
					if('perspective' == $aInput['type'] ){//若为透视图的x轴, 则跳过该搜索条件过滤
						if($groupField == $aInput['table'][$mainTable]['xdimen_key'][0])continue;
						$sValTmp = $aDataTmp[$groupField];
//						foreach($aDataTmp[$groupField] as $sKeyTmp =>$sValTmp){
							$aConcatKey[$nIndex][$dimens['dimenkey2field'][$groupField]][$sValTmp] = $sValTmp;
//						}
////						$aConcatKey = $aConcatKeyNew;
					}else
						$aConcatKey[$nIndex][$dimens['dimenkey2field'][$groupField]][$j] = $aDataTmp[$groupField];
				}
			}
		}
//		var_export($aConcatKey);
//				if('perspective' == $aInput['type'] && $group==$aInput['table'][$mainTable]['xdimen_key'][0])//
//		var_export($aConcatKey);
	
		
		$type='flash';
		$aConf=$this->getConf($type);
		$aDates=$this->getDate($type);
		$aFlashConfig=array();
		foreach($aDates as $k=>$Date){
			foreach($aConf as $table =>$aConfigTmp){
				$aFlashConfig[$k][$table]=$aConfigTmp;
				$aCondition=$aConfigTmp['condition'];
				$aCondition=array_merge(array($Date),$aCondition);
				$aFlashConfig[$k][$table]['condition']=$aCondition;
			}
		}
//		var_export($aInput['table'][$mainTable]['xdimen_key'][0]);
		
		$aConfSeconds=array();
		$aSortIndexs = $this->aInput['input'][$type]['sort_indexs'];
		$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug( $aSortIndexs, 'a', 'aSortIndexs');
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');		
		$aListDataCondition=array();
		$aFlashSeries = array();//flash legend列表中的翻译
		if(count($aConcatKey)>0){
			foreach($aConcatKey[$nIndex] as $group =>$val){
				$quotes=$oDict->ALYSgetDimenTypeOf($group);
				$k=0;
				foreach($val as $v){
					if(in_array($k, $aSortIndexs)){
						$aLabel = $oId2Label->ALYSchgId2Label(array($group), array($group=>array($v)));
						$aFlashSeries[$k][] = $aLabel[$group][$v];
						$groupCondition[$k]=$group ." in (".$quotes.$v.$quotes.")";
						$aListDataCondition[$k][] = $groupCondition[$k];
					}
					$k++;
				}
			}
		}
		//重写flash的series栏
		$this->aInput['input'][$type]['seriestips'] = array();
		foreach($aFlashSeries as $i=>$row){
			$this->aInput['input'][$type]['seriestips'][] = join(",", $row);
		}
//		unset($aListDataCondition[1]);

//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aFlashConfig, 'a', 'aFlashConfig');
		$nIndexSecond = 0;
		foreach($aFlashConfig[$nIndex] as $table =>$aConfigTmp){
			foreach($aListDataCondition as $sort_i =>$aTmp){
				$aCondition=$aConfigTmp['condition'];
				if($this->aInput['input'][$type]['sort_totalflag'] && $nIndexSecond==0){//若需要统计总计
					$aConfSeconds[$nIndexSecond][$table] = $aConfigTmp;
					array_unshift($this->aInput['input'][$type]['seriestips'], \YcheukfReport\Lib\ALYS\ALYSLang::_('SORT_TOTAL'));
					$nIndexSecond++;
				}
				foreach($aListDataCondition[$sort_i] as $aConditionTmp){
					$aCondition[] = $aConditionTmp;
				}
				$aConfSeconds[$nIndexSecond][$table]=$aConfigTmp;
				$aConfSeconds[$nIndexSecond][$table]['condition']=$aCondition;
				$nIndexSecond++;
			}
		}
//		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aConfSeconds, 'a', 'aConfSeconds');
		foreach($aConfSeconds as $k =>$aConf){
			foreach($aConf as $table=>$aConfigTmp){
				list($aData[$k][$table], $nTmpTotal) = $this->$type->getAlldata($aConfigTmp);
			}
		}
		$aData=$this->_executeSqlData($type,$aData);

		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);	
		return $aData;

	}
	
}
?>