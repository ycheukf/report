<?php
namespace YcheukfReport\Lib\ALYS;
/**
	@version V1.0 Nov 2011   (c) 2011-2012 (allyes.com). All rights reserved.
	报表通用类
 */

class Report{
	
	public $resourceSplitChar = '__resource__';
	public $splitChar = '__split__';
	
	public function __construct(){
		
	
	}
	
	
	/**
	*  根据__分割数组得到最后的值
	*/
	public function ALYSsep__value($v){
		if(strstr($v,'__')){
			$aValue=explode('__',$v);
			return $aValue[count($aValue)-1];
		
		}else{
			return $v;
		}
	}
	
	
	//取得表的表示日期的字段名
	public static function getDateFeildByTable($type='',$table=''){
		if(empty($type)||empty($table)){
			return 	\YcheukfReport\Lib\ALYS\ALYSConfig::get('dateField');
		}
		$Input = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aInput = $Input['input'][$type];
		$dateFeild = @$aInput['table'][$table]['dateFeild'];
		if(empty($dateFeild)){
			$dateFeild=\YcheukfReport\Lib\ALYS\ALYSConfig::get('dateField');
		}
		return $dateFeild;
	}

}
?>