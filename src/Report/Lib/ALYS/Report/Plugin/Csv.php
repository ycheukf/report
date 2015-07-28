<?php
/**
* CSV的输出的PLUGIN
*
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Csv extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}



	/**
	 *	负责csv的字符串的组织与输出

	 @return string 处理过后的csv 字符串
	 */

	function ALYSfmtOutputCsv(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();


		$sCsv = "\xEF\xBB\xBF";
		if($aInput['nodateFlag'] == false){
			foreach($aInput['date'] as $aTmp){
				$sCsv .= "#".$aTmp['s']."~".$aTmp['e']."\r\n";
			}
			$sCsv .= "\r\n\r\n";
		}
		if(isset($aOutput['flash.output'])){
			$sCsv .= $aOutput['flash.output'];
			$sCsv .= "\r\n\r\n";
		}
		if(isset($aOutput['total.output'])){
			$sCsv .= $aOutput['total.output'];
			$sCsv .= "\r\n\r\n";
		}
		if(isset($aOutput['detail.output'])){
			$sCsv .= $aOutput['detail.output'];
			$sCsv .= "\r\n\r\n";
		}

		return $sCsv;
	}



	function ALYSfmtListTitle($arrGroup)
	{
		return $arrGroup;
	}


	function ALYSfmtListData($arrListData)
	{
		return $arrListData;
	}
}
?>