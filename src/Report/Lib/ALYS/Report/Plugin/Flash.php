<?php
/**
* FLASH 样式控制的plugin
*
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/

namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Flash extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}

	/**
	* 格式化flash输出的样式的样式

	* @return array 两维数组, 第一位为chart级别, 第二位为data级别
	*/
	public function ALYSoutput_flash_trend_html_style(){
		  //////////chart 级别样式/////
		$aChartStyle = array(
			"MSCombi2D" =>array(
				//普通属性
				"chartLeftMargin" => 0,
				"chartRightMargin" => 0,
				"formatNumberScale" => 0,
				"showAlternateHGridColor" => 1,
				"labelDisplay" => "STAGGER",
				"isSliced" => 1,
				"slicingDistance" => 20,
				"decimalPrecision" => 0,
				"baseFontSize" => 12,
				"showShadow" => 0,
//				"yAxisMaxValue" => 100,//y轴最大值


				//菜单关于的相关属性
				"aboutMenuItemLabel" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_ABLEL'),
				"aboutMenuItemLink" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_LINK'),
				"aboutIdiggerURL" => 1,

				//导出的相关属性
				"exportShowMenuItem" => 0,
				"exportEnabled" => 1,
				"exportfilename" => 'ALYSimage_',
				"exportAction" => 'save',
				"exportCallback" => 'myexportCallBack',
				"exportDialogColor" => 'e1f5ff',
				"exportDialogBorderColor" => '0372ab',
				"exportDialogFontColor" => '0372ab',
				"exportDialogPBColor" => '0372ab',
				"exportDialogMessage" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_EXPORT_MSG'),
				//"exportHandler" => LIB_PATH_ALYS."/FCPHPClassCharts/ExportHandlers/PHP/FCExporter.php",



		),
		"Bubble" =>array(
				//"chartRightMargin" => "10",
				"divLineColor" => "c3c3c3",
				"catVerticalLineColor" => "ff0000",
				"bgColor" => "ffffff",
				"canvasBorderColor"=> '666666',
				"alternateHGridColor" => 'f0fff0',

				//"alternateHGridAlpha" => "100",

				"showBorder" => '0',
				"numberSuffix" => '%',
				"bubbleScale" => "1",
				"baseFontColor"=> '000000',
				"palette" => '3',
				"is3D" => 1,

				"animation" => 1,
				'clipBubbles' => 1,

				//'yAxisMinValue' => '0',
				//'yAxisMaxValue' => '10',
				//'xAxisMinValue' => '10',
				//'xAxisMaxValue' => '40',
				'showPlotBorder' => '1',
				//'xAxisName' => '转发数',
				//'yAxisName' => '粉丝数'
				'exportEnabled' => '1',
				'exportfilename'=>'ALYSimage_',
				'exportAction'=>'save',
				'exportCallback'=>'myexportCallBack',

		),
		"Bar2d" =>array(
				'numberSuffix' => '%',
				//"tipTitle" => "粉丝性别",
				/*
				"tipTitleColor" => "0069b7",
				"chartTopMargin" => "19",
				"baseFontSize"=> '12',
				"baseFontColor" => '333333',

				"chartRightMargin" => '200',
				"divLineAlpha" => '0',
				"alternateVGridAlpha" => "0",
				"useRoundEdges"=> '0',
				"showPlotBorder" => '0',
				"plotFillRatio" => 180,

				"canvasBorderAlpha" => 0,
				'canvasBorderThickness' => 1,

				'bgColor' => 'ffffff',
				'showBorder' => '0',
				'showVLineLabelBorder' => '0',
				'yAxisMinValue' => '0',
				'yAxisMaxValue' => '1000',
				'numberSuffix' => '',
				'showValues' => '0',
				'formatNumberScale' => '0',*/
				'bgSWF' => '',
				'exportEnabled' => '1',
				'exportfilename'=>'ALYSimage_',
				'exportAction'=>'save',
				'exportCallback'=>'myexportCallBack',


		),
		 "geography" => array(//地域
			"formatNumberScale" => '0',
			"animation" => '1',
			"showShadow" => '1',
			"baseFontSize" => '12',
			"showMarkerLabels" => '0',
			"fillColor" => 'F1f1f1',
			"borderColor" => '000000',
			"markerBorderColor" => '000000',
			"markerBgColor" => 'FF5904',
			"markerRadius" => '6',
			"legendPosition" => 'RIGHT',
			"useHoverColor" => '1',
			"showMarkerToolTip" => '1',
			"showBevel" => '1',
			"bgColor" => 'e4f4fc',


			//菜单关于的相关属性
			"aboutMenuItemLabel" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_ABLEL'),
			"aboutMenuItemLink" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_LINK'),
			"aboutIdiggerURL" => 1,

			//导出的相关属性
			"exportShowMenuItem" => 0,
			"exportEnabled" => 1,
			"exportfilename" => 'ALYSimage_',
			"exportAction" => 'save',
			"exportCallback" => 'myexportCallBack',
			"exportDialogColor" => 'e1f5ff',
			"exportDialogBorderColor" => '0372ab',
			"exportDialogFontColor" => '0372ab',
			"exportDialogPBColor" => '0372ab',
			"exportDialogMessage" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_EXPORT_MSG'),
			//"exportHandler" => LIB_PATH_ALYS."/FCPHPClassCharts/ExportHandlers/PHP/FCExporter.php",
		  ),
		 "pie3D" =>array(
			 //普通属性(饼图样式)
			"palette" => '4',
			"decimals" =>'0',
			"enableSmartLabels" =>'0',
			"enableRotation" =>'0',
			"bgColor" =>'FFFFFF',
			"bgAlpha" =>'40,100',
			"bgRatio" =>'0,100',
			"bgAngle" =>'360',
			"showBorder" =>'0',
			"startingAngle" =>'70',
			"baseFontSize" =>'12',
			"showValues" =>'0',
			"showLabels" =>'0',
			"formatNumberScale" =>'0',

			//菜单关于的相关属性
			"aboutMenuItemLabel" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_ABLEL'),
			"aboutMenuItemLink" => \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_ABOUT_LINK'),
			"aboutIdiggerURL" => 1,
		),
		);

		$aChartStyle['MultiAxisLine'] = $aChartStyle["MSCombi2D"];
		$aChartStyle['MSCombiDY2D'] = $aChartStyle["MSCombi2D"];
//		$aChartStyle['MSCombiDY2D']['leftYAxis'] = 'M,K,L';
		$aChartStyle['MSCombiDY2D']['useRoundEdges'] = '1';
		$aChartStyle['MSCombiDY2D']['sNumberSuffix'] = '';

//

		//自定义样式
		$aChartStyleCustom=array(
			"MSCombi2D" =>array(
				//iDigger theme blue 样式
				"showLegend" => 1,//是否显示legend
				"useRoundEdges" => "1",
	//			"bgColor" => "#ffffff",
				"showBorder" => "0",
				"divLineColor" => "#cccccc",
				"numDivLines"=> 5,
				"numVDivLines" => 5,

				"showAlternateHGridColor" => "0",

				"canvasPadding" => 10,
				"canvasLeftMargin" => 90,
				"canvasBorderThickness" => "0",
				"canvasBgAlpha"=> 0,
	//			"chartLeftMargin" => 10,
				"chartRightMargin" => 40,

				"bgSWFAlpha" => 100,
				//"bgSWF" => "../ext/images/bg-themeblue1.png",
			),

			"Bubble" =>array(
				"chartRightMargin" => "10",
				"divLineColor" => "c3c3c3",
				"catVerticalLineColor" => "ff0000",
				"bgColor" => "ffffff",
				"canvasBorderColor"=> '666666',
				"alternateHGridColor" => 'f0fff0',

				"alternateHGridAlpha" => "100",

				"showBorder" => '0',
				"numberSuffix" => '%',
				"bubbleScale" => "1",
				"baseFontColor"=> '000000',
				"palette" => '3',
				"is3D" => 1,

				"animation" => 1,
				'clipBubbles' => 1,
				'yAxisMinValue' => '0',
				'yAxisMaxValue' => '6',
				'xAxisMinValue' => '10',
				'xAxisMaxValue' => '100',
				'showPlotBorder' => '1',
				'xAxisName' => 'Stickiness',
				'yAxisName' => 'Cost Per Service'
			),

			"MultiAxisLine" =>array(
				"canvasBorderColor" => "#cccccc",
				"canvasBorderThickness" => "2",
				"showAlternateVGridColor" => "1",
				"alternateVGridColor" => "#FF3333",
				"useRoundEdges" => "1",
				"showBorder" => "0",
				"bgColor" => "#ffffff",
				"canvasBgColor" => "#ffffff",


	//			"numVDivLines" => "5",
	//			"numDivLines" => "2",
	//			"showAlternateVGridColor" => "1",
			),
			"geography" =>array(
				"bgColor" => "ffffff",
//				'range' => '5hundred', //5hundred|percent
				"showBorder" => "0",
				"canvasBorderColor" => "#ffffff",
			),
		);

		//自定义flash属性 可传入 $aInput['custom']['flashstyle']
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		if(isset($aInput['custom'])&&!empty($aInput['custom']['flashstyle'])&&is_array($aInput['custom']['flashstyle'])){
			foreach($aInput['custom']['flashstyle'] as $key => $val){
				if(is_array($val)){
					foreach($val as $k=>$v){
						$aChartStyleCustom[$key][$k] = $v;
					}
				}
			}
		}

		if(isset($aChartStyleCustom["Bubble"])&&isset($aChartStyle["Bubble"]))
		{
			$aChartStyle["Bubble"]=array_merge($aChartStyle["Bubble"],$aChartStyleCustom["Bubble"]);
		}
		if(isset($aChartStyleCustom["MSCombi2D"])&&isset($aChartStyle["MSCombi2D"]))
		{
			$aChartStyle["MSCombi2D"]=array_merge($aChartStyle["MSCombi2D"],$aChartStyleCustom["MSCombi2D"]);
		}
		if(isset($aChartStyleCustom["MultiAxisLine"])&&isset($aChartStyle["MultiAxisLine"]))
		{
			$aChartStyle["MultiAxisLine"]=array_merge($aChartStyle["MultiAxisLine"],$aChartStyleCustom["MultiAxisLine"]);
		}
		if(isset($aChartStyleCustom["geography"])&&isset($aChartStyle["geography"]))
		{
			$aChartStyle["geography"]=array_merge($aChartStyle["geography"],$aChartStyleCustom["geography"]);
		}
		if(isset($aChartStyleCustom["MSCombiDY2D"])&&isset($aChartStyle["MSCombiDY2D"]))
		{
			$aChartStyle["MSCombiDY2D"]=array_merge($aChartStyle["MSCombiDY2D"],$aChartStyleCustom["MSCombiDY2D"]);
		}

		  //////////data 级别样式/////
		$aDataStyle = array(
			array(
				'color' => '1874cd',
				'alpha' => "70",
				'anchorRadius' => 5,
				'lineThickness' => 3,
				'anchorBgColor' => 'ffffff',
				'anchorBorderColor' => '1874cd',
			),
			array(
				'color' => '008b00',
				'alpha' => '70',
				'anchorRadius' => 4,
				'lineThickness' => 2,
				'anchorBgColor' => '008b00',
				'anchorBorderColor' => 'ffffff',
			),
			array(
				'color' => 'ff7f24',
				'alpha' => '70',
				'anchorRadius' => 4,
				'lineThickness' => 1,
				'anchorBgColor' => 'ffffff',
				'anchorBorderColor' => 'ff7f24',
			),
			array(
				'color' => '87ceeb',
				'anchorBgColor' => '87ceeb',
				'anchorBorderColor' => 'ffffff',
			),
			array(
				'color' => '40e0d0',
				'anchorBgColor' => 'ffffff',
				'anchorBorderColor' => '40e0d0',
			),
		);

		return array($aChartStyle, $aDataStyle);
	}
	/**
	* 格式化 flash输出的html
	*
	* @param string xmlData flash中的字符串
	* @param string flashType flash类型
	* @param height int 该flash所展示的高度
	* @return array array(html字符串,装载该flash的div id)
	*/
	function ALYSfmtOutputFusionScript($xmlData,$flashType,$height){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$html = '';
		$src = $_ALYSconfig['fusion']['src'][$flashType];
		$callpdf = $_ALYSconfig['fusion']['exportCallpdf'];
		$ChartNoDataText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_ChartNoDataText");
		$PBarLoadingText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_PBarLoadingText");
		$XMLLoadingText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_XMLLoadingText");
		$ParsingDataText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_ParsingDataText");
		$RenderingChartText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_RenderingChartText");
		$LoadDataErrorText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_LoadDataErrorText");
		$InvalidXMLText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_InvalidXMLText");
		$divId = 'ALYSfc_'.uniqid();
		$sServerUrl = $_SERVER['PHP_SELF'].(empty($_SERVER['QUERY_STRING'])?'':'?'.$_SERVER['QUERY_STRING']);
		$html = <<<OUTPUT
			<div id='{$divId}_div' class='ALYSflash_trend_div'></div>
			<script language='JavaScript' type='text/javascript'>
			if(!ALYSre_export)
				var ALYSre_export = {chartObjects:[]};
			if(!ALYSre_export.pdf_export_location)
				ALYSre_export.pdf_export_location = "{$sServerUrl}";
			(function(){
				var oChart = new FusionCharts("{$src}?ChartNoDataText={$ChartNoDataText}&PBarLoadingText={$PBarLoadingText}&XMLLoadingText={$XMLLoadingText}&ParsingDataText={$ParsingDataText}&RenderingChartText={$RenderingChartText}&LoadDataErrorText={$LoadDataErrorText}&InvalidXMLText={$InvalidXMLText}", '{$divId}', '100%', '{$height}', '0', '1', '','noScale');
				oChart.setTransparent(1);
				oChart.setDataXML("{$xmlData}");
				oChart.render('{$divId}_div');
			})();
			</script>
OUTPUT;
			return  array($html,$divId);
	}


	/**
	 *	负责转换维度配置至所需要的格式

	 @param array a 当前核心类所处理的数据结构
	 @return array 处理过后的数据结构
	 */
	public function ALYSfmt_dimen($aRe){
		return $aRe;
	}


	/**
	 *	负责转换指标配置至所需要的格式

	 @param array a 当前核心类所处理的数据结构
	 @return array 处理过后的数据结构
	 */
	public function ALYSfmt_metric($aRe){
		return $aRe;
	}

	/**
	 *	负责配置饼图的高度
	 @return array 处理过后的数据结构
	 */
	public function ALYSoutput_flash_pie3D($cnt=5){
		//可以根据$cnt（数据条数）定义flash图的高度
		if($cnt<5){
			$ssheight = 150;
		}else if($cnt<8&&$cnt>=5){
			$ssheight = 250;
		}else{
			$ssheight = 300;
		}
		$height = array(
			"pie3D" =>150,
			"SSGrid" => $ssheight
		);
		return $height;
	}


	/**
	 *	格式化series的html提示
	 @return array 处理过后的数据结构
	 */
	function ALYSfmtSeriesTips($aShowField){

		list($aChartStyles, $aTrendStyle) = $this->ALYSoutput_flash_trend_html_style();
		$sHtml = "<ol>";
		foreach($aShowField as $i=>$row){
			$sHtml .= "<li _color='#".$aTrendStyle[$i]['color']."'><b></b>".\YcheukfReport\Lib\ALYS\ALYSLang::_($row)."</li>";
		
		}
		$sHtml .= "<ol>";
		return $sHtml;
	}


	/**
	* 处理特殊字符串
	*/
	public static function ALYShtmlspecialchars($str){
		if($str){
			$str = htmlspecialchars($str,ENT_QUOTES);
			//+号处理, 该+号无法在fusionchart中显示
			$str = str_replace('+','%2B',$str);
		}
		return $str;
	}
}
?>