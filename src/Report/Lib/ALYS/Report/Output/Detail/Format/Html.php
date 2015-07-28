<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Html extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){

		$th ='';
		$sOutputFormat = strtolower($this->aInput['output']['format']);
		
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin('detail');
		//标题列
		$th .= $oPlugin->ALYSfmt_list_title($this->aInput['groups'],$sOutputFormat);
		

		//根据不同的输出类型处理是否要出现饼图/柱状图
		if($this->aInput['input']['detail']['showBarColFlag']){
			$sHTML = "";
			$keys = array_keys($this->aInput['groups']['metric']);
			for($i=0; $i<count($this->aInput['groups']['metric']); $i++){
				$j = $keys[$i];
				$aGroup = $this->aInput['groups']['metric'][$j];
				if(!$aGroup['pieFieldAble'])continue;
				if(isset($aGroup['ispercent'])&&false==$aGroup['ispercent'])continue;
				$selected = $this->aInput['input']['detail']['selected'];
				if($aGroup['key'] == $selected){
					$selected = 'selected';
				}
				//$selected = $aGroup['pieFieldSelected'] ? 'selected':'';
				$sHTML .= '<OPTION VALUE="'.$aGroup['key'].'" '.$selected.'>'.\YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']).'</OPTION>';
			}
			$th .= "\n<th nowrap class='thPieList' class='td250'>".\YcheukfReport\Lib\ALYS\ALYSLang::_('percent').
				"<SELECT class='pieList'>$sHTML</SELECT></th>";
			
		}
		

		////表格主体////
		$aryListData = $this->aOutput['detail'];
		//列表内容的HTML生成
		$html = $oPlugin->ALYSfmt_list($aryListData);
		
		//外包装
		$sContent = $oPlugin->ALYSfmt_list_table($th,$html);
		
		$this->aOutput['detail.output'] = $sContent;  
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
//		var_export($this->aOutput);
	}
}
?>