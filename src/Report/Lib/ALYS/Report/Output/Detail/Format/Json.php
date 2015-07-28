<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail\Format;
class Json extends \YcheukfReport\Lib\ALYS\Report\Output\Detail\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aryListData = $aOutput['detail'];
		$xmlDataEntity = "";
		for($i=0; $i<count($aryListData); $i++){
			if(isset($aryListData[$i]) && is_array($aryListData[$i]))
			{
				foreach($aryListData[$i] as $key =>$aTmp){
					foreach($aTmp as $ii => $item){
						$colspan = isset($item['colspan'])?$item['colspan']:1;
						if($colspan==0)continue;//被前面的td跨列

						$label = isset($item['label'])?$item['label']:'';
						//$label = strip_tags(preg_replace('/'.$separator.'/', " ", $label));
						$label = strip_tags(str_replace("\t", " ", $label));
						$label = strip_tags(str_replace("\r\n", " ", $label));
						$label = strip_tags(str_replace("\n", " ", $label));

						$xmlDataEntity .= "\n\t\t<column>".$label."<column>";
					}
				}
			}
			$xmlDataEntity .= "\n\t</entity>\n\t<entity>";
		}
		$xmlDataEntity = substr($xmlDataEntity, 0, strrpos($xmlDataEntity, "<entity"));
		$aRe = array(
			"title" => 'xx',
			"data" => empty($xmlDataEntity)?"<entitys></entitys>": "<entitys>\n\t<entity>".$xmlDataEntity."\n</entitys>",
			'pagination'=>array(
				'listCount'=>$listCount,
				'items_per_page'=>$aParm['items_per_page'], 
				'current_page'=>$aParm['current_page'], 
			),
		
		);
//		var_export($aRe);
		$aOutput['detail.output'] = $aOutput['detail'];
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
}
?>