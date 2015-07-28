<?php
/**
* 静态输出的plugin
* 
* 负责XML输出的扩展
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Statichtml extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}


	/**
	 *	负责静态html报表输出时的处理

	 @param string s 将要输出的html
	 @return string 处理过后的html
	 @description 传进来的html中已经埋了一些可替换的字符串, 用于不同系统的不同需求的替换 
	 */
	public function ALYSbefore_output($s){
		$s = str_replace('[replace_title]', 'my default plugintitle', $s);
		$s = str_replace('[replace_css_file]', '', $s);
		$s = str_replace('[replace_script_file]', "<script src='http://10.0.3.219/team/feng/fusion/JSClass/FusionCharts.js'></script>", $s);
		$s = str_replace('[replace_script]', '', $s);
		$s = str_replace('[replace_style]', '', $s);
		return $s;
	}
	
	
	/**
	*  格式化list输出的HTML字符串
	*	
	* @param string sListHtml 
	* @return string 输出的html字符串
	*/

	function ALYSfmtOutputList($sListHtml){
		
		$sListHtml = '<div class="static_list">'.$sListHtml.'</div>';
		
		return $sListHtml;
	}
	
	/**
	*  格式化total输出的HTML字符串
	*	
	* @param string sTotalHtml 
	* @return string 输出的html字符串
	*/

	function ALYSfmtOutputTotal($sTotalHtml){
		
		$sTotalHtml = '<div class="static_total">'.$sTotalHtml.'</div>';
		
		return $sTotalHtml;
	}
	
	/**
	*  格式化list输出的HTML字符串
	*	
	* @param string sFlashHtml 
	* @return string 输出的html字符串
	*/

	function ALYSfmtOutputFlash($sFlashHtml){
		
		$sFlashHtml = '<div class="static_flash">'.$sFlashHtml.'</div>';
		
		return $sFlashHtml;
	}
	
}
?>