<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Pdf extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	function go(){				
		$headertop ="";	  //设置头标题上部份
		$headerfoot="";	  //设置头标题下部份
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();	 
		$aOutput['total.output'] = self::_forma_pdf_total($aOutput['total.output']);
		$aOutput['detail.output']  = self::_format_pdf_list($aOutput['detail.output']);
		if(isset($aInput["custom"]["image"])&&!empty($aInput["custom"]["image"])){
			$image = $aInput["custom"]["image"];
			$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
			\YcheukfReport\Lib\ALYS\ALYSFunction::removeLifeFile($_ALYSconfig['pdf']['path'],$_ALYSconfig['pdf']['lifetime'],$_ALYSconfig['pdf']['remove']);
			if(isset($aInput["custom"]["headertop"])|| isset($aInput["custom"]["headerfoot"])){
				$headertop =  $aInput["custom"]["headertop"];
				$headerfoot=  $aInput["custom"]["headerfoot"];
				$pagefooter=  @$aInput["custom"]["pagefooter"];
			}else{
				$aHeader = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Css");
				$aHeader = $aHeader ->ALYSfmtOuputPdfTitle();
				$headertop = $aHeader["titletop"];
				$headerfoot= $aHeader["titlefoot"];
				$pagefooter= @$aHeader["pagefooter"];
			}
			$aOutput['output'] = self::_createPdf($image,$aOutput['total.output'],$aOutput['detail.output'],"f",$headertop,$headerfoot,$pagefooter);
			
			\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
		}	
	}
	//替换total样式
	function _forma_pdf_total($_sStr){
		$aCssConfig = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Css");
		$aCss = $aCssConfig->ALYSfmtOuputTotalPdf(); 			
		$_sStr = str_replace(array_keys($aCss),array_values($aCss),$_sStr);	
		return 	$_sStr;
	}
	//替换list样式
	function _format_pdf_list($_sStr){
		$aCssConfig = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Css");
		$aCss = $aCssConfig->ALYSfmtOuputListPdf();			
		$_sStr =  str_replace(array_keys($aCss),array_values($aCss),$_sStr);	
		return 	$_sStr;
	}
	/**
	*	@生成PDF文件
	**/
	function _createPdf($sFlash,$aTotal,$aList,$visitType,$top,$headFoot,$pageFooter=''){
		$pdfname = 'allyes_'.uniqid().".pdf";
		/**
		* @获取图表基础数据
		**/
		$css_total = $aTotal;
		$css_list  = $aList;
		/**
		* @初使PDF对象
		* 默认是I：在浏览器中打开，D：下载，F：在服务器生成pdf ，S：只返回pdf的字符串
		**/
		$pdf = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("pdf");
		$_pdfname = $pdf->setInfo($pdfname,strtoupper($visitType),$top,$headFoot,$pageFooter);
		$pdf->addPage();
		/**
		* @初使图表配置样式
		**/
//		$aCssConfig = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Formatpdf");
//		$aCss = $aCssConfig->ALYSload_style();
//
//		$akv = array();
//		// 处理基本数据与样式的搭配(1)
//		$akv = $pdf->aRecur($aCss['total']);
//		$css_total = $pdf->replaceCss($akv,$total);
//
//		// 处理基本数据与样式的搭配(2)
//		$akv = $pdf->aRecur($aCss['detail']);
//		$css_list = $pdf->replaceCss($akv,$list);
//
//		//处理图片
		$css_flash = $pdf->setImg($sFlash);

		//进一步处理不规则样式
//		$css_list	= preg_replace("/\<div class\=\"color\"\>\<\/div\>/i","",$css_list);
//		$css_total	= preg_replace("/<div class=\"mdata\">/i","",$css_total);
//		$css_total	= preg_replace("/\<\/div\>/i","",$css_total);

		//将数据添加至PDF类，使其形成PDF文件
		$pdf->writeHTML(\YcheukfReport\Lib\ALYS\ALYSLang::_("PDF_FLASH_TITLE"));$pdf->Ln();
		$pdf->writeHTML($css_flash);
		$pdf->writeHTML(\YcheukfReport\Lib\ALYS\ALYSLang::_("PDF_TOTAL_TITLE"));$pdf->Ln();
		$pdf->writeHTML($css_total);
		$pdf->writeHTML(\YcheukfReport\Lib\ALYS\ALYSLang::_("PDF_LIST_TITLE"));$pdf->Ln();
		$pdf->writeHTML($css_list);
		$pdf->read();
		return $_pdfname;
	}


	function _listDemo(){
		return '<table class=\'ALYStable\'><thead><tr><th nowrap class="autoWidth"><div class="color"></div><div>NO <span class="popgroup" style="clear:both;">select</span></div></th><th nowrap  class=\'sortable\' key="media" ><div>media<span  class=\'jp_tip\' title="media-tip" >&nbsp;</span></div></th><th nowrap  class=\'sortable\' key="adv" ><div>adv<span  class=\'jp_tip\' title="adv-tip" >&nbsp;</span></div></th><th nowrap  class=\'sortable\' key="bounceRate" ><div>bounceRate<span  class=\'jp_tip\' title="bounceRate-tip" >&nbsp;</span></div></th><th nowrap  class=\'sortable\' key="pageView" ><div>pageView<span  class=\'jp_tip\' title="pageView-tip" >&nbsp;</span></div></th><th nowrap  class=\'sortable\' key="ns2visitor" ><div>ns2visitor<span  class=\'jp_tip\' title="ns2visitor-tip" >&nbsp;</span></div></th><th nowrap  class=\'sortable\' key="nc" ><div>nc<span  class=\'jp_tip\' title="nc-tip" >&nbsp;</span></div></th></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;1\'><b class=\'no\'><b style=\'background-color: #ca9d50\'>&nbsp;</b>1</b></td><td align=\'left\'   class=\'td150\'  title=\'187,baidu\'>187,baidu</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'10349718\'>10349718</td><td align=\'right\'   class=\'td150\'  title=\'57.75%\'>57.75%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'183684\'>183684</td><td align=\'right\'   class=\'td150\'  title=\'0.72%\'>0.72%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;2\'><b class=\'no\'><b style=\'background-color: #bddef9\'>&nbsp;</b>2</b></td><td align=\'left\'   class=\'td150\'  title=\'204,autohome\'>204,autohome</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'32598179\'>32598179</td><td align=\'right\'   class=\'td150\'  title=\'72.57%\'>72.57%</td><td align=\'right\'   class=\'current_even td150\'  title=\'104863\'>104863</td><td align=\'right\'   class=\'td150\'  title=\'0.14%\'>0.14%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;3\'><b class=\'no\'><b style=\'background-color: #f8cb00\'>&nbsp;</b>3</b></td><td align=\'left\'   class=\'td150\'  title=\'179,pcauto\'>179,pcauto</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'2122697\'>2122697</td><td align=\'right\'   class=\'td150\'  title=\'92.94%\'>92.94%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'72106\'>72106</td><td align=\'right\'   class=\'td150\'  title=\'2.6%\'>2.6%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;4\'><b class=\'no\'><b style=\'background-color: #9dc800\'>&nbsp;</b>4</b></td><td align=\'left\'   class=\'td150\'  title=\'177,qq\'>177,qq</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'3822222\'>3822222</td><td align=\'right\'   class=\'td150\'  title=\'60.97%\'>60.97%</td><td align=\'right\'   class=\'current_even td150\'  title=\'41769\'>41769</td><td align=\'right\'   class=\'td150\'  title=\'0.31%\'>0.31%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;5\'><b class=\'no\'><b style=\'background-color: #ff9f54\'>&nbsp;</b>5</b></td><td align=\'left\'   class=\'td150\'  title=\'178,xcar\'>178,xcar</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'27349747\'>27349747</td><td align=\'right\'   class=\'td150\'  title=\'98.12%\'>98.12%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'39840\'>39840</td><td align=\'right\'   class=\'td150\'  title=\'0.14%\'>0.14%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;6\'><b class=\'no\'><b style=\'background-color: #00a8a8\'>&nbsp;</b>6</b></td><td align=\'left\'   class=\'td150\'  title=\'91,Aj11_Baidu\'>91,Aj11_Baidu</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'80.09%\'>80.09%</td><td align=\'right\'   class=\'current_even td150\'  title=\'34495\'>34495</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;7\'><b class=\'no\'><b style=\'background-color: #de5c5c\'>&nbsp;</b>7</b></td><td align=\'left\'   class=\'td150\'  title=\'175,sohu\'>175,sohu</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'15093344\'>15093344</td><td align=\'right\'   class=\'td150\'  title=\'98.79%\'>98.79%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'28694\'>28694</td><td align=\'right\'   class=\'td150\'  title=\'0.18%\'>0.18%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;8\'><b class=\'no\'><b style=\'background-color: #b070b0\'>&nbsp;</b>8</b></td><td align=\'left\'   class=\'td150\'  title=\'173,新浪_ebo\'>173,新浪_ebo</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'4541033\'>4541033</td><td align=\'right\'   class=\'td150\'  title=\'86.45%\'>86.45%</td><td align=\'right\'   class=\'current_even td150\'  title=\'18018\'>18018</td><td align=\'right\'   class=\'td150\'  title=\'0.33%\'>0.33%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;9\'><b class=\'no\'><b style=\'background-color: #78a442\'>&nbsp;</b>9</b></td><td align=\'left\'   class=\'td150\'  title=\'214,bitauto\'>214,bitauto</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'93.35%\'>93.35%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'11223\'>11223</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;10\'><b class=\'no\'><b style=\'background-color: #c2ba00\'>&nbsp;</b>10</b></td><td align=\'left\'   class=\'td150\'  title=\'186,ucar\'>186,ucar</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'216982\'>216982</td><td align=\'right\'   class=\'td150\'  title=\'89.72%\'>89.72%</td><td align=\'right\'   class=\'current_even td150\'  title=\'5534\'>5534</td><td align=\'right\'   class=\'td150\'  title=\'2.15%\'>2.15%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;11\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>11</b></td><td align=\'left\'   class=\'td150\'  title=\'395,爱结网\'>395,爱结网</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'95.84%\'>95.84%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'2493\'>2493</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;12\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>12</b></td><td align=\'left\'   class=\'td150\'  title=\'211,eastmoney\'>211,eastmoney</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'50752\'>50752</td><td align=\'right\'   class=\'td150\'  title=\'67.14%\'>67.14%</td><td align=\'right\'   class=\'current_even td150\'  title=\'464\'>464</td><td align=\'right\'   class=\'td150\'  title=\'0.53%\'>0.53%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;13\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>13</b></td><td align=\'left\'   class=\'td150\'  title=\'201,YOUKU\'>201,YOUKU</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'660236\'>660236</td><td align=\'right\'   class=\'td150\'  title=\'64.77%\'>64.77%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'266\'>266</td><td align=\'right\'   class=\'td150\'  title=\'0.01%\'>0.01%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;14\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>14</b></td><td align=\'left\'   class=\'td150\'  title=\'180,cheshi\'>180,cheshi</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'81.03%\'>81.03%</td><td align=\'right\'   class=\'current_even td150\'  title=\'72\'>72</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;15\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>15</b></td><td align=\'left\'   class=\'td150\'  title=\'194,21cn\'>194,21cn</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'65.52%\'>65.52%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'50\'>50</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;16\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>16</b></td><td align=\'left\'   class=\'td150\'  title=\'661,东方财富网\'>661,东方财富网</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'1871339\'>1871339</td><td align=\'right\'   class=\'td150\'  title=\'53.85%\'>53.85%</td><td align=\'right\'   class=\'current_even td150\'  title=\'26\'>26</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;17\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>17</b></td><td align=\'left\'   class=\'td150\'  title=\'413,Gter\'>413,Gter</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'418176\'>418176</td><td align=\'right\'   class=\'td150\'  title=\'88.24%\'>88.24%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'19\'>19</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;18\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>18</b></td><td align=\'left\'   class=\'td150\'  title=\'198,ku6\'>198,ku6</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'0.00%\'>0.00%</td><td align=\'right\'   class=\'current_even td150\'  title=\'8\'>8</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_0\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;19\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>19</b></td><td align=\'left\'   class=\'td150\'  title=\'182,ifeng\'>182,ifeng</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'0\'>0</td><td align=\'right\'   class=\'td150\'  title=\'25.00%\'>25.00%</td><td align=\'right\'   class=\'current_odd td150\'  title=\'7\'>7</td><td align=\'right\'   class=\'td150\'  title=\'0%\'>0%</td></tr><tr class=\'report_table_tr_1\'   align=\'right\'><td align=\'center\'   class=\'fline\'  title=\'&nbsp;20\'><b class=\'no\'><b style=\'background-color: #009fde\'>&nbsp;</b>20</b></td><td align=\'left\'   class=\'td150\'  title=\'185,51auto\'>185,51auto</td><td align=\'left\'   class=\'td150\'  title=\'54,易步关联\'>54,易步关联</td><td align=\'right\'   class=\'td150\'  title=\'1771\'>1771</td><td align=\'right\'   class=\'td150\'  title=\'66.67%\'>66.67%</td><td align=\'right\'   class=\'current_even td150\'  title=\'4\'>4</td><td align=\'right\'   class=\'td150\'  title=\'0.17%\'>0.17%</td></tr></table>';
	}
	function _totalDemo(){
		return '
  <TABLE class="ALYShtml_total"><tr class=\'report_table_tr_1\'>
                 <td class="label" >
					<span>页面浏览</span>
					<span class="jp_tip" title="访客浏览的页面次数。" >&nbsp;</span>
				</td>
				<td class="value"  style="white-space:nowrap;" nowrap>                
				<div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>6,743,783</span> </span>
                </div>               </td>
                 <td class="label" ><span>访客</span><span class="jp_tip" title="网站的访问人数（人次）。单天的访客是绝对唯一的(唯一Cookie数）。 " >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>3,428,678</span> </span>
                </div>               </td>
                 <td class="label" ><span>平均访问深度</span><span class="jp_tip" title="访客每次访问网站平均查看的唯一页面数。单页的重复查看次数不计算在内。公式=总访问深度/总访问数" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>1.63</span> </span>
                </div>               </td></tr><tr class=\'report_table_tr_0\'>
                 <td class="label" ><span>平均停留时间</span><span class="jp_tip" title="访客每次访问网站平均花费的时间。公式=总停留时间/总访问数" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>00:00:32</span> </span>
                </div>               </td>
                 <td class="label" ><span>总访问数</span><span class="jp_tip" title="访客对网站的访问总数。" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>3,582,235</span> </span>
                </div>               </td>
                 <td class="label" ><span>新访客页面浏览</span><span class="jp_tip" title="首次访问您网站的访问者浏览的页面次数。" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>6,235,260</span> </span>
                </div>               </td></tr><tr class=\'report_table_tr_1\'>
                 <td class="label" ><span>新访客</span><span class="jp_tip" title="首次访问您网站的访问者数目。（唯一Cookie数）" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>3,277,422</span> </span>
                </div>               </td>
                 <td class="label" ><span>IP数</span><span class="jp_tip" title="网站访问者IP数。" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>3,132,148</span> </span>
                </div>               </td>
                 <td class="label" ><span>网站跳失率</span><span class="jp_tip" title="单页访问次数（即访问者从进入页面离开网站的访问次数）所占的百分比。公式=网站所有“孤岛页面”的页面浏览数/网站所有“接入页面”的页面浏览数" >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>69.62%</span> </span>
                </div>               </td></tr><tr class=\'report_table_tr_0\'>
                 <td class="label" ><span>回访率</span><span class="jp_tip" title="回访者占所有访问者的百分比。（访客-新访客）/访客 " >&nbsp;</span></td>
<td class="value"  style="white-space:nowrap;" nowrap>                <div class="mdata">
                    <span class="mtd1"><span>&nbsp;</span></span>
                    <span class="mtd2"><span>4.41%</span> </span>
                </div>               </td><td class="cl3 c">&nbsp;</td><td class="value">&nbsp;</td><td class="cl3 c">&nbsp;</td><td class="value">&nbsp;</td></tr>
  </TABLE>';
	}

	function _flashDemo(){
		return 'http://pic5.nipic.com/20100119/4221069_120310160340_2.jpg';
	}
}
?>