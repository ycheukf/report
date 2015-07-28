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
	
	//��ѯ͸��������
	protected function _init(){
		$aInput = $this->aInput['input'][$this->_type];
		$mainTable=$aInput['mainTable']['table'];
		
		//��ѯ����
		$dataCondition = $this->getDateNew($this->_type);//��������
		$aCondition=$this->getCondition($this->_type,$mainTable);//������������
		$aCondition = array_merge(array($dataCondition[0][$mainTable]),$aCondition);
		$aConftionAry = $this->getCondition($type,$table, 'array');
		$aConftionAry = array_merge(array($dataCondition[0][$mainTable]),$aConftionAry);
		
		//ά��
		$aDimenField = $aInput['tables'][$mainTable]['dimen']['field'];
		//$aDimenKey = $aInput['tables'][$mainTable]['dimen']['key'];
		$aDimenKey2Field = $aInput['tables'][$mainTable]['dimen']['dimenkey2field'];
		//var_dump($aInput['tables'][$mainTable]['dimen']);exit;
		//��dimen�ֶε����һ����Ϊ����ά�� ����ά��ֻ��һ��
		$xDimenKey = @$aInput['table'][$mainTable]['xdimen_key'][0];
		$xDimen = $aDimenKey2Field[$xDimenKey];
		//$xDimenKey = array_pop($aDimenKey);
		if(empty($xDimen)){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','need xdimen');
		}
		//����Ϊ����ά��
		$yDimensKey = $aInput['table'][$mainTable]['ydimen_key'];
		$yDimens = array();//var_dump($yDimensKey,$aDimenKey2Field);exit;
		foreach($yDimensKey as $v){
			$yDimens[] = $aDimenKey2Field[$v]." as ".$v;
		}
		//$yDimensKey = $aDimenKey;
		$this->aInput['input'][$this->_type]['dimenpage']['yDimens'] = $yDimens;//���� �б�չʾ��
		$distinctYDimen = implode(',',$yDimens);
		
		
		//��ѯ������ʾ��
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
		
		//��ѯ������ʾ��
		//$items_per_page = 5;//�̶���ʾ������
		//$this->aInput['input'][$this->_type]['dimenpage']['items_per_page']=$items_per_page;
		$Conf['start']=(int)($aInput['dimenpage']['current_page']*$aInput['dimenpage']['items_per_page']);//������ʼֵ
		$Conf['length'] = $aInput['dimenpage']['items_per_page'];//������ʾ��
		if(!empty($aInput['dimenpage']['orderby'])){
			$Conf['orderby'] = $aInput['dimenpage']['orderby'];
		}else{
			unset($Conf['orderby']);//�������
		}
//		$Conf['field'] = 'distinct '.$xDimen." as ".$xDimenKey;
		$Conf['field_array'] = array('distinct '.$xDimen." as ".$xDimenKey);
		list($aXData,$iXTotal) = $this->list->getAlldata($Conf);
		$xfieldData = $this->_fmtArray($aXData,$xDimenKey);//var_dump($aYData,$aXData);exit;
		
		//var_dump($xfieldData,$yfieldData);exit;
		//��������ѯ������
		$aFilter = array_merge($this->_getInSQL($yfieldData,$aDimenKey2Field),$this->_getInSQL($xfieldData,$aDimenKey2Field));
		if(empty($this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'])){
			$this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'] = $aFilter;
		}else{
			$this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'] 
			= array_merge($this->aInput['input'][$this->_type]['tables'][$mainTable]['filter'],$aFilter);
		}
		
		//�������� ���ڷ�ҳ
		$this->aInput['input'][$this->_type]['page']['is_limit']=0;
		$this->aInput['input'][$this->_type]['page']['total']=$iYTotal;
		$this->aInput['input'][$this->_type]['dimenpage']['total']=$iXTotal;
		
		//��������ʾ�ֶδ���input �� dimenpage����
		$this->aInput['input'][$this->_type]['dimenpage']['yfield'] = $aYData;
		$this->aInput['input'][$this->_type]['dimenpage']['xfield'] = $xfieldData[$xDimenKey];
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}
	
	//��ʽ������
	private function _fmtArray($aData,$type){
		//var_dump($aData,$type);
		$returnData = array();
		if(is_array($aData)){
			foreach($aData as $v){
				if(is_array($type)){
					//����
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
	
	//���������IN������SQL
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