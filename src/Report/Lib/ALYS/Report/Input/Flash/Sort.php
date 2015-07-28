<?php
namespace YcheukfReport\Lib\ALYS\Report\Input\Flash;
class Sort extends \YcheukfReport\Lib\ALYS\Report\Input\Flash{

	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	* ������Ĵ���
	* ����
	*/
	public function chkInputParam($type){
		$aTables=$this->aInput['input']['detail'];
		if(count($aTables)<=0){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_depend','detail');
		}
	}
	
	/**
	*��չ�����е��������
	*/
	public function fmtInput(){

		parent::fmtInput();
		$type='flash';
		$aDefaultIndex = array();
		for($i=0; $i<$this->aInput['input']['detail']['page']['total']; $i++){
			$aDefaultIndex[] = $i;
		}
		if(isset($this->aInput['input'][$type]['sort_indexs'])){//�û��������б�Ĳ���
			$this->aInput['input'][$type]['sort_indexs']  =  count($this->aInput['input'][$type]['sort_indexs']) < $this->aInput['input']['detail']['page']['total'] ? $this->aInput['input'][$type]['sort_indexs'] : $aDefaultIndex;
		}else{//Ĭ��
			$this->aInput['input'][$type]['sort_indexs'] = ($this->aInput['input']['detail']['page']['total']>4 ? array(0,1,2,3,4) : $aDefaultIndex);
		}
		//��ʼ�� sortģʽ�µļ���Ĭ��ѡ��������Ŀ
		$this->aInput['input'][$type]['sort_totalflag']  = isset($this->aInput['input'][$type]['sort_totalflag']) ? $this->aInput['input'][$type]['sort_totalflag'] : 0;
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
//		var_export($this->aInput['input']['detail']['page']['total']);
	}
}
?>