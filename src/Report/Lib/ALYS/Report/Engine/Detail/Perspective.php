<?php
namespace YcheukfReport\Lib\ALYS\Report\Engine\Detail;
/**
* report list with mult table
*
* @author   feng <ycheukf@gmail.com>
* @package  ALYSReport
* @access   public
*/
class Perspective extends \YcheukfReport\Lib\ALYS\Report\Engine\Detail{
	public function __construct(){
		parent::__construct();
	}
	
	//查询透视依据项
	protected function _init(){
		$aInput = $this->aInput['input'][$this->_type];
		$mainTable=$aInput['mainTable']['table'];
		
		//查询条件
		$dataCondition = $this->getDateNew($this->_type);//日期条件
		$aCondition=$this->getCondition($this->_type,$mainTable);//其它过滤条件
		$aCondition = array_merge(array($dataCondition[0][$mainTable]),$aCondition);
		$aConftionAry = $this->getCondition($type,$table, 'array');
		$aConftionAry = array_merge(array($dataCondition[0][$mainTable]),$aConftionAry);
		
		//维度
		$aDimenField = $aInput['tables'][$mainTable]['dimen']['field'];
		//$aDimenKey = $aInput['tables'][$mainTable]['dimen']['key'];
		$aDimenKey2Field = $aInput['tables'][$mainTable]['dimen']['dimenkey2field'];
		//var_dump($aInput['tables'][$mainTable]['dimen']);exit;
		//将dimen字段的最后一个作为横向维度 横向维度只有一个
		$xDimenKey = @$aInput['table'][$mainTable]['xdimen_key'][0];
		$xDimen = $aDimenKey2Field[$xDimenKey];
		//$xDimenKey = array_pop($aDimenKey);
		if(empty($xDimen)){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','need xdimen');
		}
		//其余为纵向维度
		$yDimensKey = $aInput['table'][$mainTable]['ydimen_key'];
		$yDimens = array();//var_dump($yDimensKey,$aDimenKey2Field);exit;
		foreach($yDimensKey as $v){
			$yDimens[] = $aDimenKey2Field[$v]." as ".$v;
		}
		//$yDimensKey = $aDimenKey;
		$this->aInput['input'][$this->_type]['dimenpage']['yDimens'] = $yDimens;//存入 列表展示用
		$distinctYDimen = implode(',',$yDimens);
		
		
		//查询纵向显示列
		$start=(int)($aInput['page']['current_page']*$aInput['page']['items_per_page']);
		$Conf = array(
			'noRecord' => 0,
			'limit' => 1,
			'start' => $start,
			'length' => (int)$aInput['page']['items_per_page'], 
			'orderby' => $aInput['orderby'],
//			'field' =>'distinct '.$distinctYDimen,
			'field_array' => array('distinct '.$distinctYDimen),
			'table' => $mainTable,
			'condition' => $aCondition,
			'condition_array' =>$aConftionAry,
		);
		list($aYData, $iYTotal) = $this->list->getAlldata($Conf);//var_dump($aYData);exit;
		$yfieldData = $this->_fmtArray($aYData,$yDimensKey);
		unset($aData);
		
		//查询横向显示列
		//$items_per_page = 5;//固定显示项数量
		//$this->aInput['input'][$this->_type]['dimenpage']['items_per_page']=$items_per_page;
		$Conf['start']=(int)($aInput['dimenpage']['current_page']*$aInput['dimenpage']['items_per_page']);//设置起始值
		$Conf['length'] = $aInput['dimenpage']['items_per_page'];//设置显示项
		if(!empty($aInput['dimenpage']['orderby'])){
			$Conf['orderby'] = $aInput['dimenpage']['orderby'];
		}else{
			unset($Conf['orderby']);//清除排序
		}
//		$Conf['field'] = 'distinct '.$xDimen." as ".$xDimenKey;
		$Conf['field_array'] = array('distinct '.$xDimen." as ".$xDimenKey);
		list($aXData,$iXTotal) = $this->list->getAlldata($Conf);
		$xfieldData = $this->_fmtArray($aXData,$xDimenKey);//var_dump($aYData,$aXData);exit;
		
		//var_dump($xfieldData,$yfieldData);exit;
		//设置主查询的条件
		$aFilter = array_merge($this->_getInSQL($yfieldData,$aDimenKey2Field),$this->_getInSQL($xfieldData,$aDimenKey2Field));
		if(empty($this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'])){
			$this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'] = $aFilter;
		}else{
			$this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'] 
			= array_merge($this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'],$aFilter);
		}
		
		//总数设置 用于分页
		$this->aInput['input'][$this->_type]['page']['is_limit']=0;
		$this->aInput['input'][$this->_type]['page']['total']=$iYTotal;
		$this->aInput['input'][$this->_type]['dimenpage']['total']=$iXTotal;
		
		//将纵向显示字段存入input 的 dimenpage数组
		$this->aInput['input'][$this->_type]['dimenpage']['yfield'] = $aYData;
		$this->aInput['input'][$this->_type]['dimenpage']['xfield'] = $xfieldData[$xDimenKey];
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}
	
	//格式化数组
	private function _fmtArray($aData,$type){
		//var_dump($aData,$type);
		$returnData = array();
		if(is_array($aData)){
			foreach($aData as $v){
				if(is_array($type)){
					//数组
					foreach($type as $tv){
						if(empty($returnData[$tv])||!in_array($v[$tv],$returnData[$tv]))$returnData[$tv][] = $v[$tv];
					}
				}else{
					if(empty($returnData[$type])||!in_array($v[$type],$returnData[$type]))$returnData[$type][] = $v[$type];
				}
			}
		}
		//print_r($returnData);print_r($aData);
		return $returnData;
	}
	
	//将数组组成IN条件的SQL
	private function _getInSQL($array,$aDimenKey2Field){
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		$sql_conf = array();
		if(is_array($array)){
			foreach($array as $k => $v){
				$quotes=$oDict->ALYSgetDimenTypeOf($k);
				$in_str = implode($quotes.','.$quotes,$v);
				$in_str = "(".$quotes.$in_str.$quotes.")";
				$sql_conf[]=array(
					'key' => $aDimenKey2Field[$k],
					'op' => 'IN',
					'value' => $in_str
				);
			}
		}
		return $sql_conf;
	}
	
	//
	private function _getTotalCount(){
		
	}
	
	protected function _setTotal($Conf,$type){}

}
?>