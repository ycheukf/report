<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format\Html;
class Fusion{
	public $aInput;
	public $aOutput;
	public $xmlData;
	public $trend_step;
	public $trendTypeStyle;
	public $trendType='trend';
	public $dateType='day';
	public $flashType='MSCombi2D';
	 
	public $chartType='MSCombi2D';
	
	public $aDatas=array();
	public $aMainTable=array();
	public $iCountDate=0;
	public $aDates=array();
	public $aStyle=array();
	public $aTrendStyle=array();
	public $aChartStyles=array();
	public $height=250;
	public $aFlashDatas=array();
	public $dimenkey2selected=array();
	
	public function __construct(){
		$this->aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$this->aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		

		$this->aFlashDatas=$this->aInput['internal']['flash']['datas'];
		$this->aDimen=array_keys($this->aInput['input']['flash']['tables'][$this->aInput['input']['flash']['mainTable']['table']]['dimen']['dimenkey2field']);
		$this->dimenkey2selected = $this->aInput['input']['flash']['tables'][$this->aInput['input']['flash']['mainTable']['table']]['dimen']['dimenkey2selected']; //维度
	}
	
	/**
	* 准备参数
	*/
	public function go_start(){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();//print_r($this->aOutput['flash']);
		$this->aDatas = $this->aOutput['flash'];
		$categoryLength = count($this->aDatas) && isset($this->aDatas[0]) ? count($this->aDatas[0]) : 0;

		$trendStyleCategoryLength = 30;
		$this->trend_step = $_ALYSconfig['fusion']['trend_step'];
		$this->trendTypeStyle = $this->aInput['input']['flash']['typeStyle'] = isset($this->aInput['input']['flash']['typeStyle']) ? $this->aInput['input']['flash']['typeStyle'] : "line";
		$this->dateType = strtolower($this->aInput['input']['flash']['groupby']);
		
//在XML中添加相关导出参数
//		$saveChartXML="exportShowMenuItem='0' exportEnabled='1' exportAtClient='0' exportfilename='".uniqid()."' exportAction='save' exportHandler='".$_ALYSconfig['fusion']['exportHandler']."' exportCallback='exportCallBack' exportDialogMessage='".\YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSFLASH_EXPORT_MSG')."' exportDialogColor='e1f5ff' exportDialogBorderColor='0372ab' exportDialogFontColor='0372ab' exportDialogPBColor='0372ab'";
		$oTrend = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("flash");
		list($aChartStyles, $aTrendStyle) = $oTrend->ALYSoutput_flash_trend_html_style();

		if($trendStyleCategoryLength < count($categoryLength)){
			$aTmpTrendStyleNew = array();
			foreach($aTrendStyle as $a){
				$a['anchorRadius'] = 1;
				$aTmpTrendStyleNew[] = $a;
			}
			$aTrendStyle = $aTmpTrendStyleNew;
		}
		return array($aChartStyles,$aTrendStyle);
		
	}
	

	
	public function go(){
		list($aChartStyles_S, $aTrendStyle) = $this->go_start();
		
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$aDate=$this->aInput['date'];
		foreach($aDate as $Date){
			$this->aDates[]="(".$Date['s'].'~'.$Date['e'].")";
		}
		
		$this->aMainTable=$this->aInput['input']['flash']['mainTable'];
		//print_r($this->aMainTable['showField']);

		$this->iCountDate=count($this->aDates);
		if($this->iCountDate>1){
			$this->trendType='multdate';
		}elseif(count($this->aMainTable['showField'])>1){
			$this->flashType='MSCombiDY2D';
//			$this->flashType='MultiAxisLine';
			$this->trendType='multmetric';
		}
		
		//单刻度
		if(isset($this->aInput['input']['flash']['scale']) && 'single'==$this->aInput['input']['flash']['scale'])
		{
			$this->flashType='MSCombi2D';
		}
		
		//新增bubble图
		if('bubble'==$this->aInput['input']['flash']['type'])
		{
			$this->flashType='Bubble';
			$this->trendType='bubble';
		}
		
		//新增横向柱状图
		if('bar2d'==$this->aInput['input']['flash']['typeStyle'])
		{
			$this->flashType='Bar2d';
			$this->trendType='bar2d';
		}
		
		$this->chartType=$this->flashType;
		
		if('geography'==$this->aInput['input']['flash']['type']){
			//地图 兼容老格式
			if($this->dateType=='country') {
				$this->chartType='geography';
				$this->flashType='FCMap_WorldwithCountries';
				$this->height=450;
			}elseif($this->dateType=='province') {
				$this->chartType='geography';
				$this->flashType='FCMap_China2';
				$this->height=450;
			}
			
			//地图 新格式
			if('world'==$this->aInput['input']['flash']['typeStyle']) {
				$this->dateType='country';
				$this->chartType='geography';
				$this->flashType='FCMap_WorldwithCountries';
				$this->height=450;
			}elseif('china'==$this->aInput['input']['flash']['typeStyle']) {
				$this->dateType='province';
				$this->chartType='geography';
				$this->flashType='FCMap_China2';
				$this->height=450;
			}
		}
		
		$this->aChartStyles=$aChartStyles_S[$this->chartType];
		$this->aTrendStyle=$aTrendStyle;
		foreach($aTrendStyle as $k=>$aTmpTrendStyle){
			$strStyle = '';
			foreach($aTmpTrendStyle as $propertityName => $value){
				$strStyle .= $propertityName."='".$value."' ";
			}
			$this->aStyle[]=$strStyle;
		}
		
		$this->go_diff();
		$this->go_end();
		

	}
	/**
	* 拼装不同的flash xml字符串.
	*/
	public function go_diff(){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$functionName=$this->trendType.'Function';
		if ($this->trendType=='trend') $functionName='multdateFunction';
		//echo "this->trendType=";print_r($this->trendType);
		
		list($categories,$datasets,$sCount)=$this->$functionName();
		$labelStep = intval(intval($sCount)/$this->trend_step);
		$categories = '<categories>'.$categories.'</categories>';
		
		$s1 = "";
//		$sUrlBase = (\YcheukfCommon\Lib\Functions::getBaseUrl($_ALYSconfig['smHandle']));
		if(count($this->aChartStyles)){
			foreach($this->aChartStyles as $k => $v){
				$s1 .= " {$k}='{$v}'";
			}
			if(isset($_ALYSconfig['fusion']['bgSWF']) and $this->flashType !='MSCombiDY2D' and $this->trendType !='bubble')
				$s1 .= " bgSWF='".$_ALYSconfig['smHandle']->get('zendviewrendererphprenderer')->basePath().'/'.$_ALYSconfig['fusion']['bgSWF']."'";
			if(isset($_ALYSconfig['fusion']['exportHandler']))
				$s1 .= " exportHandler='".$_ALYSconfig['fusion']['exportHandler']."'";
		}
		
		//bubble图读取chart属性中的动态配置 主要是标题
		if('bubble'==$this->trendType){
			$sXkey = $this->aMainTable['showField'][0];
			$sYkey = $this->aMainTable['showField'][1];
			$seresName=\YcheukfReport\Lib\ALYS\ALYSLang::_($sXkey);			
			$s1 .= " xAxisName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars(\YcheukfReport\Lib\ALYS\ALYSLang::_($sXkey))."'";
			$s1 .= " yAxisName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars(\YcheukfReport\Lib\ALYS\ALYSLang::_($sYkey))."'";
		}
		
		$this->xmlData = "<chart labelStep='".$labelStep."' {$s1}>".$categories.$datasets.'</chart>';

	}

	/**
	* 准备多指标的Flash数据
	*/
	public function multmetricFunction(){
		$sCount=0;
		$categories=$datasets='';
		$trendTypeStyleGlobal = $this->trendTypeStyle;
		
		$iStyle = 0;//不同指标或多时段比较的样式控制
		
		switch($this->flashType){
			case 'MSCombi2D':
			case 'MSCombiDY2D':
				$aleftYaxis = array("P","S","M","K","L");
				$sDimen = $this->aDimen[0];
				if(isset($this->aMainTable['showField']) && is_array($this->aMainTable['showField'])){
					foreach($this->aMainTable['showField'] as $date_i=> $showField){
						foreach($this->aInput['input']['flash']['table'] as $table=> $aTmp){
							foreach($aTmp['metric'] as  $aTmp2){
								if($aTmp2['key'] == $showField)
									$trendTypeStyle = isset($aTmp2['trendTypeStyle']) ? $aTmp2['trendTypeStyle'] : $trendTypeStyleGlobal;
							}
						}					
				//var_dump($this->aStyle);
//						$seresName = $this->aInput['input'][$type]['seriestips'];
						$seresName=\YcheukfReport\Lib\ALYS\ALYSLang::_($showField);
						$datasets .= "<dataset  alpha='70' parentYAxis='".$aleftYaxis[$date_i]."' seriesName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($seresName)."' renderAs='".$trendTypeStyle."' showValues='0' showPlotBorder='1' drawAnchors='1' {$this->aStyle[$iStyle]} >";
						if(!empty($this->aDatas[$date_i]) && is_array($this->aDatas[$date_i])){
							
							if(!empty($sDimen)){
								foreach($this->aDatas[$date_i] as $key=>$value){
									$aIDs[$sDimen][] = $key;
								}
								$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
								$aLabel = $oId2Label->ALYSchgId2Label($this->dimenkey2selected, $aIDs);
							}
							foreach($this->aDatas[$date_i] as $date=>$Data){
								$value=$this->aFlashDatas[$date_i][$date][$showField];
								$datasets .= "<set value='".$value."' label='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($Data[$showField])."' xAxisLabel='".(empty($sDimen)?\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date):\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$date]))."'/>";
								
								if($date_i==0){
									$categories .= "<category  label='".(empty($sDimen)?\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date):\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$date]))."'/>";
									$sCount++;
								}
							}
							
						}else{
							$datasets .= "<set />";
							$categories .= "<category />";
							$this->aChartStyles['showToolTip'] = '0';
						}
						$datasets .= '</dataset>';
						$iStyle++;
					}		
				}
				break;
				
			default:
			case 'MultiAxisLine':
				if(isset($this->aMainTable['showField']) && is_array($this->aMainTable['showField'])){
					foreach($this->aMainTable['showField'] as $date_i=> $showField){
						$seresName=\YcheukfReport\Lib\ALYS\ALYSLang::_($showField);
						$datasets .= "<axis title='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($seresName)."' titlePos='right' axisOnLeft='".(($date_i+1)%2)."' numDivLines='4'  divlineisdashed='1' divLineColor='696969' color='".$this->aTrendStyle[$date_i]['color']."'>";
						$datasets .= "<dataset seriesName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($seresName)."' renderAs='".$trendTypeStyleGlobal."' showValues='0' showPlotBorder='1' drawAnchors='1' {$this->aStyle[$iStyle]}  >";
						if(!empty($this->aDatas[$date_i]) && is_array($this->aDatas[$date_i])){
							foreach($this->aDatas[$date_i] as $date=>$Data){
								$value=$this->aFlashDatas[$date_i][$date][$showField];
								$datasets .= "<set value='".$value."' label='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($Data[$showField])."' xAxisLabel='".$date."'/>";
								
								if($date_i==0){
									$categories .= "<category  label='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date)."'/>";
									$sCount++;
								}
							}
							
						}else{
							$datasets .= "<set />";
							$categories .= "<category />";
							$this->aChartStyles['showToolTip'] = '0';
						}
						$datasets .= '</dataset>';
						$datasets .= "</axis>";
						$iStyle++;
					}		
				}
				break;
		}

		
		return array($categories,$datasets,$sCount);
	}
	
	/**
	* 准备普通，多时段的Flash数据
	*/
	public function multdateFunction(){
		$sCount=0;
		$iStyle = 0;//不同指标或多时段比较的样式控制
		$categories=$datasets='';
		$sDimen = $this->aDimen[0];
//						$seresName = $this->aInput['input'][$type]['seriestips'];
		if(is_array($this->aDatas)&&!empty($this->aDatas)){
			foreach($this->aDatas as $date_i => $aData){
				$sDate=$this->aInput['date'][$date_i]['s'];
				$eDate=$this->aInput['date'][$date_i]['e'];
				foreach($this->aMainTable['showField'] as $j=> $showField){
					
					
					$k=$this->iCountDate*$j+$date_i;
					$seresName = $this->aInput['input']['flash']['seriestips'][$date_i];
					$seresName = \YcheukfReport\Lib\ALYS\ALYSLang::_($seresName);
					if($this->trendType=='multdate') $seresName .=$this->aDates[$date_i];
					$datasets .= "<dataset seriesName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($seresName)."'  renderAs='".$this->trendTypeStyle."' showValues='0' showPlotBorder='1' drawAnchors='1' {$this->aStyle[$k]}  >";
					//Id2label
					if(!empty($aData)&&is_array($aData)){
						if(!empty($sDimen)){
							foreach($this->aDatas[$date_i] as $key=>$value){
								$aIDs[$sDimen][] = $key;
							}
							$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
							$aLabel = $oId2Label->ALYSchgId2Label($this->dimenkey2selected, $aIDs);
						}
//							var_export($aLabel);
//							var_export($sDimen);
						foreach($aData as $date=>$Data){
							if($this->dateType == 'week'){
								
								$xAxisLabel = $this->_getWeekPeriod($date, 1, $sDate, $eDate);
								$category = $this->_getWeekPeriod($date, 0, $sDate, $eDate);
							}else if($this->dateType == 'month'){
								$xAxisLabel = $this->_getMonthPeriod($date, 1, $sDate, $eDate);
								$category = $this->_getMonthPeriod($date, 0, $sDate, $eDate);
							}else{
								//$category = $xAxisLabel = $date;
								$xAxisLabel=(empty($sDimen)?\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date):\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$date]));
								$category=(empty($sDimen)?\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date):\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$date]));
							}
							$value=$this->aFlashDatas[$date_i][$date][$showField];
							$datasets .= "<set value='".$value."' label='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($Data[$showField])."' xAxisLabel='".$xAxisLabel."'/>";
							
							if($date_i==0){
								$categories .= "<category  label='".$category."'/>";
								$sCount++;
							}
							
						}
					}else{
						$datasets .= "<set />";
						$categories .= "<category />";
						$this->aChartStyles['showToolTip'] = '0';
					}
					$datasets .= '</dataset>';
					$iStyle++;
				}
			}
		}else{
			$i=0;
			foreach($this->aMainTable['showField'] as $j=> $showField){
				$seresName=\YcheukfReport\Lib\ALYS\ALYSLang::_($showField);
				$datasets .= "<dataset seriesName='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($seresName)."' showValues='0' showPlotBorder='1' drawAnchors='1' ".$this->aStyle[$i++]."><set /></dataset>";
				$categories .= "<category label='' />";
				$this->aChartStyles['showToolTip'] = '0';
			}
		}
		
		return array($categories,$datasets,$sCount);
	}
	
	/**
	* 准备多指标的Flash数据
	* #edit by Paul 2012.5.11
	*/
	public function bubbleFunction(){
		//bubble动态配置读取
		$iXLines = 6;//x轴线条数 默认
		$iXLines = empty($this->aChartStyles['xlabelScales'])?$iXLines:(int)$this->aChartStyles['xlabelScales'];
		unset($this->aChartStyles['xlabelScales']);
		
		$identityNode = '';//需要标识的结点
		$sCount=0;
		$categories=$datasets='';
		
		$toolText_split = '{br}';
		
		//异常处理
		if(count($this->aMainTable['showField'])!=3){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_METRIC_NUM_WRONG','有且只能为3个');
		}
		//取得标蓝的ID
		$sBoldID = @$this->aInput['input']['flash']['boldID'];
		if(is_array($sBoldID)){
			$aBoldIDs = $sBoldID;
		}else{
			$aBoldIDs = array($sBoldID);
		}
		
		//x y z 对应的key
		$sXkey = $this->aMainTable['showField'][0];
		$sYkey = $this->aMainTable['showField'][1];
		$sZkey = $this->aMainTable['showField'][2];
		
		$sXkeyLang = \YcheukfReport\Lib\ALYS\ALYSLang::_($sXkey);
		$sYkeyLang = \YcheukfReport\Lib\ALYS\ALYSLang::_($sYkey);
		$sZkeyLang = \YcheukfReport\Lib\ALYS\ALYSLang::_($sZkey);
		
		//x 的最大值
		$iX = $iY = 0;
		$aX = $aY = $aZ = array();
		if(is_array($this->aDatas)){
			foreach($this->aDatas as $key => $v){
				if(!empty($v[$sXkey])){
					if($v[$sXkey]>$iX)$iX=$v[$sXkey];
					$aX[] = $v[$sXkey];
				}
				if(!empty($v[$sYkey])){
					if($v[$sYkey]>$iY)$iY=$v[$sYkey];
					$aY[] = $v[$sYkey];
				}
				if(!empty($v[$sZkey])){
					$aZ[] = $v[$sZkey];
				}
			}	
		}
		
		//确定每条线增量
		$iXPlus = ceil($iX/$iXLines);
		//chart样式设置最大值最小值
		$this->aChartStyles['xAxisMaxValue'] = $iX>0?(int)($iX+$iXPlus):10;
		$this->aChartStyles['yAxisMaxValue'] = $iY>0?(int)($iY+ceil($iY/4)):10;
		//设置成偶数
		if(($this->aChartStyles['yAxisMaxValue'])%2!=0){
			$this->aChartStyles['yAxisMaxValue']++;	
		}
		$_labelSuffix = $this->aChartStyles['xlabelSuffix']?$this->aChartStyles['xlabelSuffix']:'';
		unset($this->aChartStyles['xlabelSuffix']);		
		
		//dataset数据
		$datasets .= "<dataset showValues='0' showLable='0' plotBorderThickness='1' plotBorderColor='ffffff' plotBorderAlpha='50' plotFillAlpha='90' >";
		$oDict = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Metric');
		if(!empty($this->aDatas) && is_array($this->aDatas)){
			$aIDs = array();
			$sDimen = $this->aDimen[0];
			foreach($this->aDatas as $key=>$value){
				$aIDs[$sDimen][] = $key;
			}
			$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
			$aLabel = $oId2Label->ALYSchgId2Label($this->dimenkey2selected, $aIDs);
			
			foreach($this->aDatas as $key=>$value){
				$sXdisplay = $sXkeyLang.':'.$oDict->ALYSmetricFormat($sXkey, $value[$sXkey]);
				$sYdisplay = $sYkeyLang.':'.$oDict->ALYSmetricFormat($sYkey, $value[$sYkey]);
				$sZdisplay = $sZkeyLang.':'.$oDict->ALYSmetricFormat($sZkey, $value[$sZkey]);
				$datasets .= "<set x='".$value[$sXkey]."' y='".$value[$sYkey]."' z='".$value[$sZkey]."' toolText='".\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$key])."{br}".$sXdisplay.$toolText_split.$sYdisplay.$toolText_split.$sZdisplay."' color='".(in_array($key,$aBoldIDs)?'1874cd':'008b00')."'/>";
				$sCount++;
			}
			
			//categories数据
			for($i=0;$i<=$iXLines;$i++){
				$categories .= "<category  label='".($i>0?\YcheukfReport\Lib\ALYS\ALYSFormat::formatNumber($i*$iXPlus).$_labelSuffix:'')."' x='".($i*$iXPlus)."' showVerticalLine='1' />";
			}
			//设置最后一条 无刻度
			$categories .= "<category  label=' ' x='".(($i+1)*$iXPlus)."' showVerticalLine='0' />";
		}else{
			$datasets .= "<set />";
		}
		
		//trend line(平均线 还有平均线上的点) 添加到dataset数据后边
		$fYAvg = $fXAvg = $fZAvg = 0;
		if(($iYcnt=count($aY))>0){
			$fYAvg = round(array_sum($aY)/$iYcnt,2);
			$sTrendline = "<trendlines><line startValue='".$fYAvg."' isTrendZone='0' displayValue='".$oDict->ALYSmetricFormat($sYkey, $fYAvg)."' color='FF0000' dashed='1' dashGap='5' /></trendlines>";
		}
		if(($iXcnt=count($aX))>0){
			$fXAvg = round(array_sum($aX)/$iXcnt,2);
			$sTrendline .= "<vTrendlines><line startValue='".$fXAvg."' isTrendZone='0'  endValue='".$fXAvg."' color='FF0000' dashed='1' dashGap='5' /></vTrendlines>";
		}
		if(($iZcnt=count($aZ))>0){
			$fZAvg = round(array_sum($aZ)/$iZcnt,2);
		}
		
		if($fYAvg>0&&$fXAvg>0){
			$sAvgXdisplay = $sXkeyLang.':'.$oDict->ALYSmetricFormat($sXkey, $fXAvg);
			$sAvgYdisplay = $sYkeyLang.':'.$oDict->ALYSmetricFormat($sYkey, $fYAvg);
			$sAvgZdisplay = $sZkeyLang.':'.$oDict->ALYSmetricFormat($sZkey, $fZAvg);
			$datasets .= "<set x='".$fXAvg."' y='".$fYAvg."' z='".$fZAvg."' toolText='".\YcheukfReport\Lib\ALYS\ALYSLang::_('Bubble_Average')."{br}".$sAvgXdisplay.$toolText_split.$sAvgYdisplay.$toolText_split.$sAvgZdisplay."' color='FF0000'/>";
		}
		
		$datasets .= '</dataset>';
		$datasets .= $sTrendline;
		return array($categories,$datasets,$sCount);
		
	}
	
	/**
	* 横向柱状图
	* #edit by Paul 2012.5.28
	*/
	public function bar2dFunction(){
		$sDimen = $this->aDimen[0];
		$sCount = 0;
		
		$this->aChartStyles['tipTitle'] = \YcheukfReport\Lib\ALYS\ALYSLang::_($sDimen);
		
		//异常处理
		if(count($this->aMainTable['showField'])!=1){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_METRIC_NUM_WRONG','有且只能为1个指标');
		}
		
		$aDimenFilter = array();
		if(!empty($this->aInput['input']['flash']['table'][$this->aMainTable['table']]['dimen'][0]['dimenFilter'])){
			$aDimenFilter = $this->aInput['input']['flash']['table'][$this->aMainTable['table']]['dimen'][0]['dimenFilter'];
		}
		
		$datasets = "";
		//var_dump($this->aDatas);
		if(isset($this->aMainTable['showField']) && is_array($this->aMainTable['showField'])){
			foreach($this->aMainTable['showField'] as $date_i=> $showField){
				//根据dimenFilter重组数据
				if(!empty($aDimenFilter)){
					if(!is_array($aDimenFilter)){
						throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','dimenFilter must be array');	
					}
					$aTmpFlash = $aTmpData = array();
					foreach($aDimenFilter as $df){
						if(empty($this->aFlashDatas[$date_i][$df][$showField])){
							$aTmpFlash[$df][$showField] = 0;
							$aTmpData[$df][$showField] = 0;
						}else{
							$aTmpFlash[$df][$showField] = $this->aFlashDatas[$date_i][$df][$showField];
							$aTmpData[$df][$showField] = $this->aDatas[$date_i][$df][$showField];
						}
					}
					$this->aDatas[$date_i] = $aTmpData;
					$this->aFlashDatas[$date_i] = $aTmpFlash;
				}
				
				if(!empty($this->aDatas[$date_i]) && is_array($this->aDatas[$date_i])){
					$iSumValue = 0;//总数 用于后边求百分比
					//调用Id2label翻译
					if(!empty($sDimen)){
						foreach($this->aDatas[$date_i] as $key=>$value){
							//$iSumValue+=$this->aFlashDatas[$date_i][$key][$showField];
							$aIDs[$sDimen][] = $key;
						}
						$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
						$aLabel = $oId2Label->ALYSchgId2Label($this->dimenkey2selected, $aIDs);
					}
					
					foreach($this->aDatas[$date_i] as $date=>$Data){
						$value=$this->aFlashDatas[$date_i][$date][$showField];
						$datasets .= "<set label='".(empty($sDimen)?\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($date):\YcheukfReport\Lib\ALYS\ALYSFunction::_htmlspecialchars($aLabel[$sDimen][$date]))."' value='".$value."' tooltext=' ' color='58b819'/>";
						$sCount++;
					}
				}else{
					//无数据
					$datasets .= "<set />";	
				}
			}		
		}
		$categories = array();
		return array($categories,$datasets,$sCount);
	}
	
	

	/**
	* 返回参数
	*/
	public function go_end(){		
		$afusion = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Flash");
		$html = array();
//		echo $this->xmlData;
		$html = $afusion->ALYSfmtOutputFusionScript($this->xmlData,$this->flashType,$this->height);
		$this->aOutput['flash.output'] = $html[0];
		$this->aOutput['flash.seriestips'] = $afusion->ALYSfmtSeriesTips($this->aInput['input']['flash']['seriestips']);
		$this->aOutput['flash.divid']  = $html[1];
		$this->aOutput['flash.src']  = $html[2];
		$this->aOutput['flash.xmlData']  = $this->xmlData;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
//		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
//		$html = '';
//		$src = $_ALYSconfig['fusion']['src'][$this->flashType];
//		$ChartNoDataText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_ChartNoDataText");
//		$PBarLoadingText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_PBarLoadingText");
//		$XMLLoadingText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_XMLLoadingText");
//		$ParsingDataText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_ParsingDataText");
//		$RenderingChartText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_RenderingChartText");
//		$LoadDataErrorText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_LoadDataErrorText");
//		$InvalidXMLText = \YcheukfReport\Lib\ALYS\ALYSLang::_("ALYSFLASH_InvalidXMLText");
//		$divId = 'ALYSfc_'.uniqid();
//		$w = '100%';
//		$h = $this->height;
//		$html = <<<OUTPUT
//			<div id='{$divId}_div' class='ALYSflash_trend_div'></div>
//			<script language='JavaScript'>
//				(function(){
//					var oChart = new FusionCharts("{$src}?ChartNoDataText={$ChartNoDataText}&PBarLoadingText={$PBarLoadingText}&XMLLoadingText={$XMLLoadingText}&ParsingDataText={$ParsingDataText}&RenderingChartText={$RenderingChartText}&LoadDataErrorText={$LoadDataErrorText}&InvalidXMLText={$InvalidXMLText}", 
//								'{$divId}', '{$w}', '{$h}', '0', '1', '','noScale'); 
//					oChart.setTransparent(1);
//					oChart.setDataXML("{$this->xmlData}");
//					oChart.render('{$divId}_div');
//				})();
//			</script>
//OUTPUT;
//		$this->aOutput['flash.output'] =  $html;
////		$this->aOutput['flash.output'] =  $this->xmlData;
////		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aOutput,'a', 'aOutputaOutput');
	}
	
	
/**
* 根据那年的那一周返回那一周的第一天和最后一天
*
*/
	public function _getWeekPeriod($yearweek, $periodFlag = 1, $sDate='', $eDate=''){  
	   $sDateStamp = strtotime($sDate);
	   $eDateStamp = strtotime($eDate);
	   $aPeriod = array(); 
	   $year = substr($yearweek,0,4).''; 
	   $week = substr($yearweek,5,2); 
	   $startDay = "Mon"; 
	   $endDay = "Sun"; 

		$iWeek = date('W', mktime(0,0,0,1,1,$year));
		$iWeekOffset = $iWeek==1?1:0;
		$startdate =  strtotime('+' . ($week-$iWeekOffset) . ' week',mktime(0,0,0,1,1,$year));  
		 
	   $enddate = $startdate; 
	   while(date("D",$startdate) != $startDay){ 
		   $startdate = mktime(0,0,0,date("m",$startdate),date("d",$startdate)-1, date("Y",$startdate));      
	   } 
	   while(date("D",$enddate) != $endDay){ 
		   $enddate = mktime(0,0,0,date("m",$enddate),date("d",$enddate)+1, date("Y",$enddate));      
	   } 

	   $startdate = $startdate<$sDateStamp?$sDateStamp:$startdate;
	   $enddate = $enddate>$eDateStamp?$eDateStamp:$enddate;

	   return $periodFlag==1?\YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSWEEKPOSITON', array(intval($week)))."(".date('Y-m-d', $startdate)."~".date('Y-m-d', $enddate).")" : $year.\YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSYEAR4SHORT').\YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSWEEKPOSITON', array(intval($week)));  
	} 


/**
* 根据那年的那一周返回那一周的第一天和最后一天
*
*/
	function _getMonthPeriod($yearmonth, $periodFlag = 1, $sDate='', $eDate=''){  
	   $sDateStamp = strtotime($sDate);
	   $eDateStamp = strtotime($eDate);
	   $aPeriod = array(); 
	   $year = substr($yearmonth,0,4).''; 
	   $month = substr($yearmonth,5,2); 

	   $startdate = mktime(0,0,0,$month, 1, $year); 
	   $enddate = strtotime($yearmonth."-".date("t", strtotime($yearmonth."-01"))); 
	   $startdate = $startdate<$sDateStamp?$sDateStamp:$startdate;
	   $enddate = $enddate>$eDateStamp?$eDateStamp:$enddate;

	   return $periodFlag==1?\YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSmonthPositon', array(intval($year), intval($month)))."(".date('Y-m-d', $startdate)."~".date('Y-m-d', $enddate).")" : \YcheukfReport\Lib\ALYS\ALYSLang::_('ALYSmonthPositon', array(intval($year), intval($month)));  
	}
	
}
