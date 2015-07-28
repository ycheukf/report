<?php
/**
* Xml 输出的plugin
* 
* 负责XML输出的扩展
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Xml extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}


	/**
	 *	负责csv输出时的表头的处理

	 @param string s 将要输出的html
	 @return string 处理过后的html
	 @description 传进来的html中已经埋了一些可替换的字符串, 用于不同系统的不同需求的替换 
	 */
	public function ALYSxml_title(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$s = "";
		foreach($aInput['date'] as $aTmp){
			$s .= "<date>".$aTmp['s']."~".$aTmp['e']."</date>\r\n";
		}
		$s = "<header>\n".$s."</header>\n\n";
//		$s .= "#some title \r\n";
		$s .= "\r\n\r\n\r\n";
		return $s;
	}
}
?>