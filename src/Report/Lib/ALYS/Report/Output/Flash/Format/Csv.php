<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format;
class Csv extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$trendType = $aInput['input']['flash']['type'] = isset($aInput['input']['flash']['type']) ? $aInput['input']['flash']['type'] : "trend";
		$separator = isset($this->aInput['output']['csvSeparator']) ? $this->aInput['output']['csvSeparator'] : "\t";
		$sCsv = $sCsvTitle = "";
		$nIndex = 0;
		
		//维度标题的标签转换
		$aInputFlash = $aInput['input']['flash'];
		$aDimen=@array_keys($aInputFlash['tables'][$aInputFlash['mainTable']['table']]['dimen']['dimenkey2field']);
		$sDimen = @current($aDimen);		
		
		switch(strtolower($trendType)){
			default:
			case "trend":
				$aId2Label = $this -> _getId2Label($sDimen,$aOutput['flash'][0]);
				if(is_array($aOutput['flash'][0])){
					foreach($aOutput['flash'][0] as $date=> $aTmp){
						if($nIndex == 0){
							$sCsvTitle .= '"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen).'"'.$separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_(current(array_keys($aTmp)))."\"\r\n";
							$nIndex = 1;
						}
						$sCsv .= '"'.(empty($aId2Label[$date])?$date:$aId2Label[$date]).'"'.$separator.'"'.current(array_values($aTmp))."\"\r\n";
					}
				}
				break;
			case "geography":
				$aId2Label = $this -> _getId2Label($sDimen,$aOutput['flash'][0]);
				if(is_array($aOutput['flash'][0])){
					foreach($aOutput['flash'][0] as $date=> $aTmp){
						$sCsvTitle .= '"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen).'"'.$separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($date)."\"\r\n";
						if(is_array($aTmp)){
							foreach($aTmp as $k => $v){
								$sCsv .= '"'.(empty($aId2Label[$k])?$k:$aId2Label[$k]).'"'.$separator.'"'.$v."\"\r\n";
							}	
						}
						
					}
				}
				break;
			case "bubble":
				$aId2Label = $this -> _getId2Label($sDimen,$aOutput['flash']);
				$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
				if(is_array($aOutput['flash'])&&!empty($aOutput['flash'])){
					$aInputFlash = $aInput['input']['flash'];
					$aDimen=array_keys($aInputFlash['tables'][$aInputFlash['mainTable']['table']]['dimen']['dimenkey2field']);
					$sDimen = $aDimen[0];
					$sCsvTitle .= \YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen);
					if(is_array($aInput['input']['flash']['mainTable']['showField'])){
						foreach($aInput['input']['flash']['mainTable']['showField'] as $v){
							$sCsvTitle .= $separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($v).'"';
						}
					}
					$sCsvTitle .= "\r\n";
					if(is_array($aOutput['flash'])){
						foreach($aOutput['flash'] as $key=> $values){
							$tmp = array();
							if(is_array($aInput['input']['flash']['mainTable']['showField'])){
								foreach($aInput['input']['flash']['mainTable']['showField'] as $v){
									if(isset($values[$v])){
										$tmp[] = $oDict->ALYSmetricFormat($v,$values[$v]);
									}else{
										$tmp[] = '';
									}
								}
							}
							$sCsv .= '"'.(empty($aId2Label[$key])?$key:$aId2Label[$key]).'"'.$separator.'"'.implode('"'.$separator.'"',$tmp)."\"\r\n";
						}
					}
				}
				
				break;
			case "multdate":
				$sCsvTitle .= '"'.\YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSDATE");
				if(is_array($aOutput['flash'])){
					foreach($aOutput['flash'] as $i=>$aTmp){
						$sDate = $aInput['date'][$i]['s'];
						$eDate = $aInput['date'][$i]['e'];
						$sTmp = $sDate."~".$eDate;
						foreach($aTmp as $date=> $aTmp){
							if($nIndex == 0){
								$sCsvTitle .= '"'.$separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen).$separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_(current(array_keys($aTmp)))."\"\r\n";
								$nIndex = 1;
							}
							$sCsv .= '"'.$date.'"'.$separator.'"'.$sTmp.'"'.$separator.'"'.current(array_values($aTmp))."\"\r\n";
						}
					}
				}
				break;
			case "multmetric":
				$aId2Label = $this -> _getId2Label($sDimen,$aOutput['flash'][0]);
				$sCsvTitle .= '"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen).'"';
				if(is_array($aOutput['flash'])){
					foreach($aOutput['flash'] as $i=>$aTmp){
						foreach($aTmp as $date=> $aTmp2){
							foreach($aTmp2 as $guiline=> $v){
								$sCsvTitle .= $separator.'"'.\YcheukfReport\Lib\ALYS\ALYSLang::_($guiline).'"';
								break;
							}
							break;
						}
					}
				}
				$sCsvTitle .= "\r\n";
				
				if(is_array($aOutput['flash'][0])){
					foreach($aOutput['flash'][0] as $date=> $aTmp){
						$sCsv .= '"'.(empty($aId2Label[$date])?$date:$aId2Label[$date]).'"';
						for($i=0; $i<count($aOutput['flash']); $i++){
							$sCsv .= $separator.'"'.current(array_values($aOutput['flash'][$i][$date])).'"';
						}
						$sCsv .= "\r\n";
					}
				}
				break;
		}
		$aOutput['flash.output'] = $sCsvTitle.$sCsv;

		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
	}
	
	function _getId2Label($sDimen,$aData){
		$aLabel[$sDimen] = array();
		if(!empty($sDimen)){
			if(is_array($aData)){
				foreach($aData as $key=>$value){
					$aIDs[$sDimen][] = $key;
				}
				$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
				$aLabel = $oId2Label->ALYSchgId2Label(array($sDimen),$aIDs);
			}
		}
		return $aLabel[$sDimen];
	}
}
?>