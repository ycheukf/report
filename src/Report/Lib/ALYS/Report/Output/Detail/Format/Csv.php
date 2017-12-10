<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Csv extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();

	}

	public function getCharset()
	{
		$sReturn = "utf8";
		if (isset($this->aInput['output']['exportInfo']['charset'])) {
			$sReturn = $this->aInput['output']['exportInfo']['charset'];
		}
		if (in_array(strtolower($sReturn), array("utf8", "utf-8"))) {
			$sReturn = "UTF-8";
		}
		return $sReturn;
	}

	function go(){

		$bIsPerspective = 'perspective'==$this->aInput['input']['detail']['type']?true : false;//是否为透视图
		$separator = $this->_getCsvSeparator();
		$csvTitle = $csvBody = $tmpTdString =  '';
		$indexNum = 1;
		$csvTitle .= " ";

		// var_dump($this->getCharset());
		// exportInfo

		$oCSVPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("csv");
		$csvBody = $oCSVPlugin->ALYSfmtOutputCsv();//waiting...
		$arrGroup = $oCSVPlugin->ALYSfmtListTitle($this->aInput['groups']);//扩展
		if(is_array($arrGroup))
		{
			foreach($arrGroup as $k => $aTmp){
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
					if($k == 'metric')
					{
						$csvTitle .=  \YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['label']). ' - ' .$label . "$separator";
					}
					else
					{
						// $csvTitle .=  \YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['label']) . "$separator";
						$csvTitle .=  \YcheukfReport\Lib\ALYS\ALYSFunction::iconv2SpecialCharset(\YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['label']), $this->getCharset()) . "$separator";
					}
				}
			}
		}
		$csvTitle .= "\r\n";

		$start=isset($this->aInput['input']['detail']['page']['startItem'])?$this->aInput['input']['detail']['page']['startItem']:0;

		$aryListData = $this->aOutput['detail'];
		$aryListData = $oCSVPlugin->ALYSfmtListData($aryListData);

		$this->aOutput['detail.num'] = count($aryListData);
		if(is_array($aryListData)){
			foreach($aryListData as $i=> $data){
				if($i==0&&$start==0)$csvBody .= $csvTitle;
				if(isset($data) && is_array($data))
				{
					foreach($data as $key =>$aTmp){
						foreach($aTmp as $ii => $item){
							$colspan = isset($item['colspan'])?$item['colspan']:1;
							if($colspan==0)continue;//被前面的td跨列

							if ((isset($item['tdKey']) && $item['tdKey']=='tdNO')) {
								continue;
							}

							$label = (isset($item['tdKey']) && $item['tdKey']=='tdNO') ? ($i+1+$start) : (isset($item['label'])?$item['label']:'');
							//$label = strip_tags(preg_replace('/'.$separator.'/', " ", $label));
							//$label = strip_tags(str_replace($separator, " ", $label));
							$label = strip_tags(str_replace("\r\n", " ", $label));
							$label = strip_tags(str_replace("\n", " ", $label));
							$sStyle = empty($style)?"":"style='{$style}'";
							$sAlign = empty($align)?"":"align='{$align}'";
							$sColspan = $colspan==1?"":"colspan='{$colspan}'";

							$csvBody .= ("\"".\YcheukfReport\Lib\ALYS\ALYSFunction::iconv2SpecialCharset(\YcheukfReport\Lib\ALYS\ALYSLang::_($label), $this->getCharset())."\"".str_repeat($separator, $colspan));
						}
					}
				}
				$csvBody .= "\r\n";
			}
		}
		// var_dump($csvBody);
		//无数据 标题加上
//		if(empty($csvBody))$csvBody = $csvTitle;
		if(empty($aryListData))$csvBody .= $csvTitle;//waiting

		$this->aOutput['detail.output'] = $csvBody;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}
}