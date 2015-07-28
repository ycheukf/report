<?php
/**
* CSS 样式控制的plugin
* 
* 
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/

namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Css extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}
	/**
	*  替换埋在total html输出中的样式句柄
	*
	*	以下样式替换可以加入任何样式，不限于class 一种
	*  @return array 返回替换数组;
	*/
	function ALYSfmtOuputTotalHtml(){
		$aCss = array(	// total 样式
			'_totaltablecss0'	=>'class="table table-bordered"  width=50% ', // table   样式
			'_totaltrcss0'		=>'', // tr0	  样式
			'_totaltrcss1'		=>'', // tr1	  样式
			'_totaltdcss0'		=>'', // td0      样式
			'_totaltdcss1'		=>'', // td1	  样式
			'_totalspancss0'	=>'', // 基数span样式
			'_totalspancss1'	=>'', // 偶数span样式
		);
		return $aCss;
	}
	/**
	*  替换埋在list html输出中的样式句柄
	*
	*	以下样式替换可以加入任何样式，不限于class 一种
	*  @return array 返回替换数组;
	*/
	function ALYSfmtOuputListHtml(){
		$aCss = array(  //list样式
			'_listtablecss0'	=>'class="table table-hover "', //table   样式
			'_listtdcss0'		=>'class="bbc"', //表头td  样式
			'_listThSpan'		=>'', //表头span样式
			'_dimenlistspan0'		=>'class="dimen"', //dimen列表span样式
			'_metriclistspan0'		=>'class="metric"', //metric列表span样式
			'_dimenlistthcss0'		=>'class="dimen"', //dimen 表头th  样式
			'_metriclistthcss0'		=>'class="metric"', //metric 表头th  样式
			'_listbodytrcss0'	=>'', //主体tr  样式
			'_listbodytdcss0'	=>'', //主体td  样式
			'_listbodybcss'		=>'', //主体b   样式
		);
		return $aCss;
	}


	/**
	*  替换埋在total pdf输出中的样式句柄
	*
	*	以下样式替换可以加入任何样式，不限于class 一种
	*  @return array 返回替换数组;
	*/
	function ALYSfmtOuputTotalPdf(){
		$aCss = array(	// total 样式
			'_totaltablecss0'	=>'border="1"', // table   样式
			'_totaltrcss0'		=>'', // tr0	  样式
			'_totaltrcss1'		=>'style="background-color:#B8F5FC;"', // tr1	  样式
			'_totaltdcss0'		=>'', // td0      样式
			'_totaltdcss1'		=>'', // td1	  样式
			'_totalspancss0'	=>'', // 基数span样式
			'_totalspancss1'	=>'', // 偶数span样式
		);
		return $aCss;
	}
	/**
	*  替换埋在list pdf输出中的样式句柄
	*
	*	以下样式替换可以加入任何样式，不限于class 一种
	*  @return array 返回替换数组;
	*/
	function ALYSfmtOuputListPdf(){
		$aCss = array(  //list样式
			'_listtablecss0'	=>'border="1"', //table   样式
			'_listtdcss0'		=>'', //表头td  样式
			'_listThSpan'		=>'', //表头span样式
			'_listspan0'		=>'', //列表span样式
			'_listtrtitle'		=>'style="background-color:#C0C0C0;"', //表头样式
			'_listthcss0'		=>'style="background-color:#C0C0C0;"', //表头th  样式
			'_listbodytrcss0'	=>'', //主体tr  样式
			'_listbodytrcss1'	=>'style="background-color:#B8F5FC;"', //主体tr  样式
			'_listbodytdcss0'	=>'', //主体td  样式
			'_listbodybcss'		=>'', //主体b   样式
		);
		return $aCss;
	}

	/**
	*  格式化pdf输出中的头部信息
	*
	*	
	*  @return array 头部信息数组;
	*/
	function  ALYSfmtOuputPdfTitle(){
		$aHeader = array(
			'titletop' => '默认的标题上部份',
			'titlefoot' => '默认的标题下部份',
		);
		return $aHeader;
	}
}
?>