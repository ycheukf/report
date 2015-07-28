<?php
/**
* 总计的PLUGIN
* 
* 
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Total extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}
	/**
	*  格式化total输出的HTML字符串
	*	
	* @param array aOffsetData 
	* @return string 输出的html字符串
	*/

	function ALYSfmtOutputHtml($aOffsetData){

		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();

		$trClass = '_totaltrcss1';
		$sHTML = "\n<tr {$trClass} >";
		switch(strtolower($aInput['input']['total']['type'])){
			default:
			case "common":
			case "multdate":
				$index = 0;
				foreach($aOutput['total'][0][0] as $metric => $vvv){//主指标
					if($index!=0 && $index%3==0){
						$trClass = $trClass=="_totaltrcss0"?'_totaltrcss1':'_totaltrcss0';
						$sHTML .= "\n</tr>\n\r<tr {$trClass} >";
					}
					$keyLabel = \YcheukfReport\Lib\ALYS\\YcheukfReport\Lib\ALYS\ALYSLang::_("".$metric);
					$keyLabelTip = \YcheukfReport\Lib\ALYS\\YcheukfReport\Lib\ALYS\ALYSLang::_("".$metric.'-tip');
					$sHTML .= "\n<td _totaltdcss0 ><span>{$keyLabel}</span><span _totalspancss0 title='{$keyLabelTip}' >&nbsp;</span></td>";
					$sTmp = "";
					foreach($aOutput['total'] as $ii => $aTmp){
						foreach($aTmp[0] as $metric2 => $vvv2){
							if($metric != $metric2)continue;
							$offerPercentHTML = "";
							if($ii != 0){//多个指标比较时才会进到这个逻辑
								if($aOffsetData[$ii][$metric]==0)
									$offerPercentCls ="zero";
								else
									$offerPercentCls = $aOffsetData[$ii][$metric]>0?"plus2":'negative';
								$offerPercentLabel = abs(round($aOffsetData[$ii][$metric], 2))."%";
								$offsetPercentHTML = "<span class=\"{$offerPercentCls}\">({$offerPercentLabel})</span>\n";
								$sTmp .= '<span _totalspancss1><span >'.$vvv2.'</span> '.$offsetPercentHTML.'</span>';
							}else{
								$sTmp .= '<span _totalspancss1><span >'.$vvv2.'</span></span><br/>';
							}
						}
					}

					$sHTML .= "\n<td _totaltdcss1 >{$sTmp}</td>";
					$index++;
				}
				break;
		}
		//补全td
		$iExtraTd = (($index%3)==0?0:3-($index%3))*2;
		$sHTML .= "\n<td colspan = '{$iExtraTd}' >&nbsp;</td>"; 
		$sHTML .= "\n</tr>";
		return "\n<table _totaltablecss0 >".$sHTML."\n</table>";
	}

}

