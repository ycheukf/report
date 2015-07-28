<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Output extends \YcheukfReport\Lib\ALYS\Report{
	public $aInput;
	public $aOutput;
	public $aMetric=array();
	public $aDimen=array();
	public $orderby='pageView';
	

	public function __construct(){
		$this->aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$this->aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		
	}
	
	
	/**
	* ��ʽС��λ����
	*/
	public function _formatMetric($type){
		$aMainTable=$this->aInput['input'][$type]['mainTable'];

		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');		
		$this->aInput['internal'][$type]['datas']=$aOrgData=$this->aOutput[$type];
		if(isset($this->aInput['input']['flash']['type']) && $this->aInput['input']['flash']['type'] == 'geography' and $type=='flash'){
			if(count($aOrgData)){
				foreach($aOrgData as $date_i=>$aData){
					if(count($aData) < 1)continue;
					foreach($aData as $field=>$data){
						if(count($data) < 1)continue;
						foreach($data as $k=>$v){
							$this->aOutput[$type][$date_i][$field][$k] = $oDict->ALYSmetricFormat($field, $v, $data); 
						}
					}
				}
			}
		}else{
			if(count($aOrgData)){
				foreach($aOrgData as $date_i=>$aData){
					if(count($aData) < 1)continue;
					foreach($aData as $date=> $data){
						if(count($aMainTable['showField']) < 1)continue;
						foreach($aMainTable['showField'] as $metric){
							$this->aOutput[$type][$date_i][$date][$metric] = $oDict->ALYSmetricFormat($metric, $data[$metric], $data); 
						}
			
					}
				}
			}
		}

		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}
	
	/**
	*��ʼ��ά�ȣ�ָ��
	*/
	public function _initDimen_Metric($type){
		
		$aInput = $this->aInput['input'][$type];
		if(isset($aInput['orderby']))
			$this->orderby=substr($aInput['orderby'],0,strpos($aInput['orderby'],' '));// ����

		$mainTable=$aInput['mainTable']['table'];
		$this->aDimen=array_keys($aInput['tables'][$mainTable]['dimen']['dimenkey2field']); //ά��
		$this->dimenkey2selected=$aInput['tables'][$mainTable]['dimen']['dimenkey2selected']; //ά��
		
//		print_r($aInput['tables'][$mainTable]['dimen']['dimenkey2field']);
		foreach($aInput['mainTable']['showField'] as $k=>$metric){ //ָ��
			if(!in_array($metric,$this->aDimen)){
				$this->aMetric[]=$metric;
			}
		}
	}
	
	/**
	* get engine name
	*/
	public function _getTimeSlot($value, $type){
		switch($type){
			case 'week':
				$nextDay = date('o-W', strtotime($value));
				break;
			case 'month':
				$nextDay = date('Y-m', strtotime($value));
				break;
			case 'quarter':
				$nMon = date('m', strtotime($value));
				switch($nMon){
					case 1:
					case 2:
					case 3:
						$nextDay = date('Y', strtotime($value))."-Q1";
						break;
					case 4:
					case 5:
					case 6:
						$nextDay = date('Y', strtotime($value))."-Q2";
						break;
					case 7:
					case 8:
					case 9:
						$nextDay = date('Y', strtotime($value))."-Q3";
						break;
					case 10:
					case 11:
					case 12:
						$nextDay = date('Y', strtotime($value))."-Q4";
						break;
				}
				break;
			case 'day':
			default:
				$nextDay = $value;
				break;
		}
		return $nextDay;
	}
	
	/**
	* ��ȡcsv�ķָ���
	*/
	function _getCsvSeparator(){
		return isset($this->aInput['output']['csvSeparator']) ? $this->aInput['output']['csvSeparator'] : "\t";
	}

	function _fmtOutput($type){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.".$type.".format.".$this->aInput['output']['format']);
		$o->go();
	}
	
}
?>