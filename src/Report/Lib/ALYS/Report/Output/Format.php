<?php
/**
	@version V1.0 Nov 2011   (c) 2011-2012 (allyes.com). All rights reserved.
	报表通用类
	负责将list,total,flash的输出格式整合
 */

namespace YcheukfReport\Lib\ALYS\Report\Output;
class Format extends \YcheukfReport\Lib\ALYS\Report\Output{

	public function __construct(){
		parent::__construct();
	}

	public function go(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.format.".$aInput['output']['format']);
		$o->go();
	}

	//替换total样式
	function _forma_html_total($_sStr){
		$aCssConfig = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Css");
		$aCss = $aCssConfig->ALYSfmtOuputTotalHtml(); 			
		$_sStr = str_replace(array_keys($aCss),array_values($aCss),$_sStr);	
		return 	$_sStr;
	}
	//替换list样式
	function _format_html_list($_sStr){
		$aCssConfig = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Css");
		$aCss = $aCssConfig->ALYSfmtOuputListHtml();			
		$_sStr =  str_replace(array_keys($aCss),array_values($aCss),$_sStr);	
		return 	$_sStr;
	}
}
?>