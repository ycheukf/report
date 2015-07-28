<?php
namespace YcheukfReport\Lib\ALYS\Report\Dictionary;
/**
* ָ�������
*
* ���𱨱��е�ָ��ת��
*
* @author   ycheukf@gmail.com
* @package  Dictionary
* @access   public
*/
class Metric extends \YcheukfReport\Lib\ALYS\Report\Dictionary{

	
	public function __construct(){
		parent::__construct();
	}

	
	/**
	* ��ʽ��ָ������
	* @param string $fieldKey ָ��key��ֵ
	* @param string $fieldValue ָ���б�ѡ�е�option
	* @param string $aRowData ���е�����
	* @return string ��ָ�����������ֵ
	*/
	public function ALYSmetricFormat($fieldKey, $fieldValue, $aRowData=array()){
		$sRe = "";
		switch($fieldKey){
			case 'nc2pv' :
			case 'bounceRate':
			case 'visiteBack'://�ٷֱ�ģʽ
				$sRe = \YcheukfReport\Lib\ALYS\ALYSFormat::percent($fieldValue);
				break;	
			case 'avgTimeLength' ://����,ʱ��ģʽ
				$sRe = \YcheukfReport\Lib\ALYS\ALYSFormat::second4Time($fieldValue);
				break;	
			case 'avgDepth' ://����С�������λ
				$sRe = \YcheukfReport\Lib\ALYS\ALYSFormat::round($fieldValue);
				break;	
			case 'pvbyvisitor' ://����С�������λ
				$sRe = $aRowData['pvbyvisitor']."[".$aRowData['visitor'].']';
				break;	
			default :
				$sRe = $fieldValue;
		
		}
		return $sRe;
	}
	
	
	/**
	* ָ��ı��ʽ, ����һЩ��Ҫ��ϼ����ָ��
	* 
	* @param string $field ָ��key��ֵ
	* @return string ��ָ����ʹ�õĹ�ʽ
	*/
	public function ALYSmetricExpresion($field){
		switch($field){
			case 'bounceRate':
				$sEval='islandPageView/entryPageView';
				break;
			case 'visiteBack':
				$sEval='(visitor-newVisitor)/visitor';
				break;
			case 'avgTimeLength':
				$sEval='sumTimeLength/sumSession';
				break;
			case 'avgDepth':
				$sEval='sumDepth/sumSession';
				break;
			case 'nc2uc':
				$sEval='nc/uc';
				break;
			case 'nc2pv':
				$sEval='pageView/nc';
				break;
			default:
				$sEval=$field;
		}
		return $sEval;

	}
	

	/**
	* ���ݴ���ı���ת���ɶ�Ӧ�ı����ֶ�
	* ָ��

	* @param string $field ָ��key��ֵ
	* @return string ����Ҫ�ı����ֶ�
	*/
	
	public function ALYSmetric2Field($field){
		//������
		if('_nosum'==substr($field,-6,6)){
			$field=preg_replace('/_nosum$/','',$field)." as ".$field;
			return $field;
		}
		
		switch($field){
			case 'bounceRate':
				$field="sum(islandPageView)/sum(entryPageView) as ".$field;
				break;
			case 'visiteBack':
				$field="(sum(visitor)-sum(newVisitor))/sum(visitor) as ".$field;
				break;
			case 'avgTimeLength' :
				$field=" sum(sumTimeLength)/sum(sumSession) as ".$field;
				break;
			case 'avgDepth' :
				$field=" sum(sumDepth)/sum(sumSession) as ".$field;
				break;
				
			case 'nc2uc' :
				$field=" sum(nc)/sum(uc) as ".$field;
				break;
			case 'nc2pv':
				$field='pageView/nc';
				break;
			case 'nc2visitor':
				$field='visitor/nc';
				break;
			case 'pvbyvisitor':
				$field = "sum(visitor) as visitor, sum(pageView) as ".$field;
				break;
			
			default :
				$field=" sum(".$field.") as ".$field;
		
		}
		return $field;
	
	
	}	
	
	/**
	* ���ݴ���ı���ת���ɶ�Ӧ�ı����ֶ�
	* ָ��

	* @param string $field ָ��key��ֵ
	* @return string ����Ҫ�ı����ֶ�
	*/
	
	public function ALYStableDefine($table){
		$condition = $this -> getConditionByInput();
		
		switch($table){
			case 'ABC':
				$table="(select * from StatsWeiboActivePost order by date desc) as ".$table;
				break;
			case 'ABC_total':
				$table="(select * from (select * from StatsWeiboActivePost where ".$condition." order by date desc) as t1 group by mid) as ".$table;
				break;
			
			
			default :
				$table=$table;
		
		}
		return $table;
	
	
	}	
	
	/**
	* ���ݴ���ı���ת���ɶ�Ӧ�ı����ֶ�
	* ά��

	* @param string $key ά��key��ֵ
	* @param string $selected ά���б�ѡ�е�option
	* @return string ����Ҫ�ı����ֶ�
	*/
	public function ALYSdimen2Field($key, $selected,$type='',$table=''){
		
		if($key == 'timeslot'){
			$dateField = \YcheukfReport\Lib\ALYS\Report::getDateFeildByTable($type,$table);
			switch ($selected){//�����б�
				case 'hour':
					$sReField = "hour";
					break;
				case 'day':
					$sReField = $dateField;
					break;
				case 'week':
					$sReField = 'DATE_FORMAT('.$dateField.', "%x-%v")';
					break;
				case 'month':
					$sReField = 'DATE_FORMAT('.$dateField.', "%Y-%m")';
					break;
				case 'quarter':
                        $sReField = "CONCAT(YEAR(`".$dateField."`),'-Q',QUARTER(`".$dateField."`))";
                        break;
				case 'year':
					$sReField = 'DATE_FORMAT('.$dateField.', "%Y")';
					break;
				default:
					$sReField = $dateField;
			}
		}elseif($selected=='province'){
			$sReField =  "CONCAT(country, province)";
		}elseif($selected=='city'){
			$sReField =  "CONCAT(country, province, city)";
		}elseif($selected=='channelGroupId'){
			$sReField =  "CONCAT(channelPoolId, '".$this->resourceSplitChar."', {$selected})";
		}elseif($selected=='dbconfigop'
			|| $selected=='currentVersion'
			|| $selected=='newestVersion'
		){
			$sReField =  "customerid";
		}else{
			$sReField = $selected;
		}
		
		return $sReField;
	}
	
	/**
	* ά�����ͣ����Σ��ַ���
	*/
	public function ALYSgetDimenTypeOf($key){
		$quotes='';
		switch ($key){
			case 'channelPoolId':
			//case 'channelGroupId':
			case 'domainId':
				$quotes="";
				break;
			default :
				$quotes="'";
				break;
		}
		
		return $quotes;
	}
	
	public function getConditionByInput(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aCondition = array();
		//����
		$dateField = \YcheukfReport\Lib\ALYS\Report::getDateFeildByTable();
		foreach($aInput['date'] as $aDate){
			$aCondition[]="(".$dateField." >= '".$aDate['s']."' and ".$dateField." <= '".$aDate['e']."')";
		}
		//filter
		if(is_array($aInput['filters'])){
			foreach($aInput['filters'] as $condition){
				$aCondition[]=$condition['key'].' '.$condition['op'].' '.$condition['value'];
			}
		}
		$sCondition = 1;
		if(is_array($aCondition)&&!empty($aCondition))$sCondition = implode(' and ',$aCondition);
		return $sCondition;
	}
	
}
?>