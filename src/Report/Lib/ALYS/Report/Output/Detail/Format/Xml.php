<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Xml extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$sXmlTh = "";
		foreach($aInput['groups'] as $k => $aTmp){
			foreach($aTmp as $key=>$aGroup){
				/**
				* aGroup 数据结构
				$aGroup = array(
					'type'=>xxx
					'key'=>xxx
					'pieFieldSelected'=>true|false 是否为在饼图中被选中的指标
					'pieFieldAble'=>true|false 是否出现在饼图的选择项中
					'sortAble'=>true|false
					'orderbyClass'=>''|orderby_asc|orderby
					'thclass'=>array()
					'tipclass'=>array()
				)
				*/
				$key = $aGroup['key'];
				$label = \YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']);
				$sXmlTh .= "\n\t\t<th>".$label."</th>";
			}
		}
		$sXmlTh = "\t<thead>\n".$sXmlTh."\n\t</thead>\n";

		$aryListData = $this->aOutput['detail'];
		$xmlDataEntity = "";
		for($i=0; $i<count($aryListData); $i++){
			if(isset($aryListData[$i]) && is_array($aryListData[$i]))
			{
				$xmlDataEntity .= "\n\t\t<entity>";
				foreach($aryListData[$i] as $key =>$aTmp){
					foreach($aTmp as $ii => $item){
						if($ii == 0 && $key=='dimens')continue;

						$label = isset($item['label'])?$item['label']:'';
						//$label = strip_tags(preg_replace('/'.$separator.'/', " ", $label));
						$label = strip_tags(str_replace("\t", " ", $label));
						$label = strip_tags(str_replace("\r\n", " ", $label));
						$label = strip_tags(str_replace("\n", " ", $label));

						$xmlDataEntity .= "\n\t\t\t<column>".$label."</column>";
					}
				}
				$xmlDataEntity .= "\n\t\t</entity>";
			}
		}
		$xmlDataEntity = "\t<data>\n".$xmlDataEntity."\n\t</data>\n";

		$xmlPage = "";
		if(isset($aInput['input']['detail']['page'])){
			foreach($aInput['input']['detail']['page'] as $key =>$v){
				$xmlPage .= "\n\t\t<{$key}>".$v."</{$key}>";
			}
		}
		$xmlPage = "\t<pagenation>\n".$xmlPage."\n\t</pagenation>\n";

//		var_export($aRe);
		$this->aOutput['detail.output'] = "<list>\n".$sXmlTh.$xmlDataEntity.$xmlPage."\n</list>\n";
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
		
	}
}
?>