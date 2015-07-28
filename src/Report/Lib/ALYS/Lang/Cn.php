<?php
namespace YcheukfReport\Lib\ALYS\Lang;

class Cn{
	function __construct(){
	}
	public static function getDict(){
		$_ALYSlang = array(
			'ALYSEXPT_INPUT_TABLE_EMPTY' => '传入的表参数不能为空',
			'ALYSEXPT_KEY_WRONG' => '请求的参数下不正确的键值',
			'ALYSEXPT_VALUE_WRONG' => '请求的参数下不正确的值',
			'ALYSEXPT_INPUT_KEY_WRONG' => "['input']下不正确的键值",
			'ALYSEXPT_INPUT_METRIC_NUM_WRONG' => "指标数量错误",
			
			
			'ALYSEXPT_INPUT_DEPEND' => "请求的参数依赖于",
			'ALYSYEAR4SHORT' => "年",
			'ALYSWEEKPOSITON' => "第[replace0]周",
			'ALYSMONTHPOSITON' => "[replace0]年[replace1]月",
			'ALYSFLASH_ABOUT_ABLEL' => '关于 IDIGGER',
			'ALYSFLASH_ABOUT_LINK' => 'http://idigger.allyes.com',
			'ALYSFLASH_EXPORT_MSG' => '请稍候，已导出：',
			'ALYSDATE' => "日期",
			'ALYSPERIOD' => "时间段",
			'ALYSGUILINE' => "指标",
		);
		return $_ALYSlang;
	}
}
//if(!isset($_ALYSlang))
//	$_ALYSlang = array();


