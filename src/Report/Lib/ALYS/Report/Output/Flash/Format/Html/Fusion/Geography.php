<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Flash\Format\Html\Fusion;
class Geography extends \YcheukfReport\Lib\ALYS\Report\Output\Flash\Format\Html\Fusion{
	public $aGeoColor = array('#D1FAB9','#A9EB72','#79CA2F','#54A90E','#32810D','#17460C', 	);
	
	public function __construct(){
		parent::__construct();
//		var_export($this->dimenkey2selected);
		if($this->dateType == 'city'){
			$sKey = key($this->dimenkey2selected);
			$this->dimenkey2selected[$sKey] = 'province';
		}
	}
	
	/**
	* 不同的部分
	*/
	public function go_diff(){
		$geoType=$this->dateType;
		$showLabels=0;
		
		$type='flash';
		$aInput = $this->aInput['input'][$type];
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$noDataShowLabel = empty($_ALYSconfig['fusion']['geography']['noDataShowLabel'])?0:1;
		$geographyRange = empty($aInput['geoRangeStyle'])?'common':$aInput['geoRangeStyle'];
		unset($this->aChartStyles['range']);
		
		$field=$aInput['mainTable']['showField'][0];
		$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
		$aTmp = $oId2Label->ALYSchgId2Label($this->dimenkey2selected);
		
		$aryLabelSet4Geo = $aTmp[key($this->dimenkey2selected)];
		$Data=$this->aDatas[0][$field];
		$DataN=$this->aFlashDatas[0][$field];
		//$Data = $DataN = array(58=>900);
//		var_dump($aTmp);
		//翻译citykey
		$sCitykey = 'num2citykey';
		$aIDs = array();		
		if(is_array($DataN)){
			foreach($DataN as $k => $v){
				$aIDs[$sCitykey][] = $k;
			}
		}
		$aLabel = $oId2Label->ALYSchgId2Label(array($sCitykey=>$sCitykey),$aIDs);
		$aCityMap = $aLabel[$sCitykey];
		//var_dump($aCityMap,$Data);exit;
		$fieldLabel=\YcheukfReport\Lib\ALYS\ALYSLang::_($field);
		if($geoType=='country'){
			$aCountrySN2Id = $this->_getCountryShortName2Id();
			$aryLabelSet4Geo = $this->_getCompatibleCode($aryLabelSet4Geo);
			foreach($aryLabelSet4Geo as $shortName => $geoLabel){
				$fusionCountryId = $this->_chgLocationCode2FusionCode($shortName);
				if(empty($aCountrySN2Id[$fusionCountryId])) continue;
				$iniId = $aCountrySN2Id[$fusionCountryId];

				if($Data[$fusionCountryId]){
					if(!empty($_ALYSconfig['fusion']['geography']['linkble'])){
						$event = ($shortName=='CN') ? "link=\\\"JavaScript:changeGeoMap('country', '".$shortName."');\\\"" : "";
					}
					$value=$DataN[$fusionCountryId];
					$entitys .= "<entity id='".$iniId."' value='".$value."' showLabel='1' displayValue='".$geoLabel."' toolText='".$fieldLabel.','.$geoLabel.','.$Data[$fusionCountryId]."' ".$event."/>";
				}else{
					$entitys .= "<entity id='".$iniId."' value='0' displayValue='".$geoLabel."' toolText='".$geoLabel."' showLabel='".$noDataShowLabel."' />";
				}

			}
		}elseif($geoType=='province'){
			unset($Data['CN00']);

			foreach($aryLabelSet4Geo as $shortName => $geoLabel){
				list($t, $geoLabel) = explode('-', $geoLabel); 
				$countryCode = substr($shortName,0,2);
				if($countryCode=='CN'){
					$provinceCode = substr($shortName,2,2);
					$fusionProvinceId = 'CN.'.$provinceCode;
					$fusionProvinceId = $this->_chgLocationCode2FusionCode($fusionProvinceId);
					if(!empty($_ALYSconfig['fusion']['geography']['linkble'])){
						$event = " link=\\\"JavaScript:changeGeoMap('province', '".$countryCode.'.'.$provinceCode."');\\\"";
					}
					if(!empty($aCityMap[$shortName]))$shortName = $aCityMap[$shortName];
					if($Data[$shortName]){
						//$selectedProvince = ($geoType == 'city' && strtoupper($provinceCode)==strtoupper($aryData['geoTypeProvinceCode']))? "fontBold='1' fontSize='16' fontColor='0' color='FF9933'":"";
						$value=$DataN[$shortName];
						$entitys .= "<entity id='".$fusionProvinceId."' value='".$value."' showLabel='1' displayValue='".$geoLabel."' toolText='".$fieldLabel.','.$geoLabel.','.$Data[$shortName]."' {$event} {$selectedProvince}/>";
					}else{
						$entitys .= "<entity id='".$fusionProvinceId."' value='0' displayValue='".$geoLabel."' toolText='".$geoLabel."' showLabel='".$noDataShowLabel."'  {$event}/>";
					}
				}else{
					unset($Data[$shortName]);
				}
			}
			$showLabels = 1;
		}
		//echo "aryLabelSet4Geo=";print_r($aryLabelSet4Geo);
		//$colorRange = $this->_getFusionColorRange($field, @max($DataN));
		if('percent'==$geographyRange){
			$colorRange = $this->_rangeColor1to10percent(@max($DataN));
		}elseif('5hundred'==$geographyRange){
			$colorRange = $this->_rangeColor1to5hun(@max($DataN));
		}else{
			$colorRange = $this->_getFusionColorRange($field,@max($DataN));
		}
		
		$s1 = "";
		$sUrlBase = (\YcheukfCommon\Lib\Functions::getBaseUrl($_ALYSconfig['smHandle']));
		if(count($this->aChartStyles)){
			foreach($this->aChartStyles as $k => $v){
				$s1 .= " {$k}='{$v}'";
			}
			if(isset($_ALYSconfig['fusion']['exportHandler']))
				$s1 .= " exportHandler='".$sUrlBase.'/'.$_ALYSconfig['fusion']['exportHandler']."'";
		}
		$this->xmlData = "<map showLabels ='".$showLabels."' legendCaption='".$fieldLabel."' {$s1}>".$colorRange."<data>".$entitys.'</data></map>';
	}
	/**
	 * 将数据库中的国家,省份code与fusionchart中的省份code对应起来
	*/
	function _chgLocationCode2FusionCode($fusionProvinceId){
		////国家////
//		if($fusionProvinceId == 'CG')	//country表里的代码
//			$fusionProvinceId = 'CD';	//flash里的代码
		if($fusionProvinceId == 'CF')
			$fusionProvinceId = 'CP';
		if($fusionProvinceId == 'IQ')
			$fusionProvinceId = 'IZ';
		if($fusionProvinceId == 'MS')
			$fusionProvinceId = 'aa';
		if($fusionProvinceId == 'MG')
			$fusionProvinceId = 'MS';
		if($fusionProvinceId == 'EH')
			$fusionProvinceId = 'WA';
		if($fusionProvinceId == 'HR')
			$fusionProvinceId = 'HY';
		if($fusionProvinceId == 'TR')
			$fusionProvinceId = 'TK';
		if($fusionProvinceId == 'YE')
			$fusionProvinceId = 'YM';
		if($fusionProvinceId == 'IR')
			$fusionProvinceId = 'IA';
		if($fusionProvinceId == 'IE')
			$fusionProvinceId = 'IR';
		if($fusionProvinceId == 'GB')
			$fusionProvinceId = 'UK';
		////中国省份////
		if($fusionProvinceId == 'CN.TW')
			$fusionProvinceId = 'CN.TA';
		if($fusionProvinceId == 'CN.XA')
			$fusionProvinceId = 'CN.SA';
		if($fusionProvinceId == 'CN.HB')
			$fusionProvinceId = 'CN.HU';
		if($fusionProvinceId == 'CN.HE')
			$fusionProvinceId = 'CN.HB';
		if($fusionProvinceId == 'CN.HA')
			$fusionProvinceId = 'CN.HE';
		if($fusionProvinceId == 'CN.HI')
			$fusionProvinceId = 'CN.HA';
		if($fusionProvinceId == 'CN.MO')
			$fusionProvinceId = 'CN.MA';
		if($fusionProvinceId == 'CN.MO')
			$fusionProvinceId = 'CN.MA';
		return $fusionProvinceId;
	}
	public function _getCountryShortName2Id()
	{
		return $aCountrySN2Id = array('AG'=>'01','BS'=>'02','BB'=>'03','BZ'=>'04','CA'=>'05','CR'=>'06','CU'=>'07','DM'=>'08','DO'=>'09','SV'=>'10','GD'=>'11','GT'=>'12','HT'=>'13','HN'=>'14','JM'=>'15','MX'=>'16','NI'=>'17','PA'=>'18','KN'=>'19','LC'=>'20','VC'=>'21','TT'=>'22','US'=>'23','GL'=>'24','AR'=>'25','BO'=>'26','BR'=>'27','CL'=>'28','CO'=>'29','EC'=>'30','FK'=>'31','GF'=>'32','GY'=>'33','PY'=>'34','PE'=>'35','SR'=>'36','UY'=>'37','VE'=>'38','DZ'=>'39','AO'=>'40','BJ'=>'41','BW'=>'42','BF'=>'43','BI'=>'44','CM'=>'45','CV'=>'46','CP'=>'47','TD'=>'48','KM'=>'49','CI'=>'50','CD'=>'51','DJ'=>'52','EG'=>'53','GQ'=>'54','ER'=>'55','ET'=>'56','GA'=>'57','GH'=>'58','GN'=>'59','GW'=>'60','KE'=>'61','LS'=>'62','LI'=>'63','LR'=>'64','MS'=>'65','MW'=>'66','ML'=>'67','MR'=>'68','MA'=>'69','MZ'=>'70','NA'=>'71','NE'=>'72','NG'=>'73','RW'=>'74','ST'=>'75','SN'=>'76','SC'=>'77','SL'=>'78','SO'=>'79','ZA'=>'80','SD'=>'81','SZ'=>'82','TZ'=>'83','TG'=>'84','TN'=>'85','UG'=>'86','WA'=>'87','ZM'=>'88','ZW'=>'89','GM'=>'90','CG'=>'91','MI'=>'92','AF'=>'93','AM'=>'94','AZ'=>'95','BD'=>'96','BT'=>'97','BN'=>'98','MM'=>'99','KH'=>'100','CN'=>'101','TP'=>'102','GE'=>'103','IN'=>'104','ID'=>'105','IA'=>'106','JP'=>'107','KZ'=>'108','KP'=>'109','KR'=>'110','KG'=>'111','LA'=>'112','MY'=>'113','MN'=>'114','NP'=>'115','PK'=>'116','PH'=>'117','RU'=>'118','SG'=>'119','LK'=>'120','TJ'=>'121','TH'=>'122','TM'=>'123','UZ'=>'124','VN'=>'125','TW'=>'126','HK'=>'127','MO'=>'128','AL'=>'129','AD'=>'130','AT'=>'131','BY'=>'132','BE'=>'133','BH'=>'134','BG'=>'135','HY'=>'136','CZ'=>'137','DK'=>'138','EE'=>'139','FI'=>'140','FR'=>'141','DE'=>'142','GR'=>'143','HU'=>'144','IS'=>'145','IR'=>'146','IT'=>'147','LV'=>'148','LN'=>'149','LT'=>'150','LU'=>'151','MK'=>'152','MT'=>'153','MV'=>'154','MC'=>'155','MG'=>'156','NL'=>'157','NO'=>'158','PL'=>'159','PT'=>'160','RO'=>'161','SM'=>'162','CS'=>'163','SK'=>'164','SI'=>'165','ES'=>'166','SE'=>'167','CH'=>'168','UA'=>'169','UK'=>'170','VA'=>'171','CY'=>'172','TK'=>'173','AU'=>'175','FJ'=>'176','KI'=>'177','MH'=>'178','FM'=>'179','NR'=>'180','NZ'=>'181','PW'=>'182','PG'=>'183','WS'=>'184','SB'=>'185','TO'=>'186','TV'=>'187','VU'=>'188','NC'=>'189','BA'=>'190','IZ'=>'191','IE'=>'192','JO'=>'193','KU'=>'194','LB'=>'195','OM'=>'196','QA'=>'197','SA'=>'198','SY'=>'199','AE'=>'200','YM'=>'201','PR'=>'202','KY'=>'203');
	}
	
	public function _getFusionColorRange($fieldLabel, $max=0){

		$offer = 1;
		switch($fieldLabel){
			case 'sony_timelength': 
			case 'avgTimeLength': 
			case 'combination_sony_timepersession': 
			case 'avgPageTime': 
			case 'visiteBack': 
				$offer = 10;
			break;
			case 'avgDepth': 
				$offer = 1;
			break;
			default:
				$offer = 100;
				break;
		}
//		var_export($offer);
		$colorRange = '';
		$averageCount = intval($max / 6);
		$averageCount = round(($averageCount+$offer)/$offer)*$offer;
		for($i=0; $i<6; $i++){
			$minValue = ($averageCount*$i);
			$colorRange .= "<color minValue='".($minValue)."' maxValue='".($averageCount*($i+1))."' displayValue='' color='".$this->aGeoColor[$i]."' />";
		}
		$colorRange = "<colorRange>{$colorRange}</colorRange>";
		return $colorRange;
	}
	//固定1-500范围
	public function _rangeColor1to5hun($max=0){

		$offer = 100;
		for($i=0; $i<5; $i++){
			$minValue = ($offer*$i);
			$colorRange .= "<color minValue='".($minValue)."' maxValue='".($offer*($i+1))."' displayValue='' color='".$this->aGeoColor[$i]."' />";
		}
		//最后那条
		$colorRange .= "<color minValue='".($offer*$i)."' maxValue='".($max+1)."' displayValue='".($offer*$i)."以上' color='".$this->aGeoColor[$i]."' />";
		$colorRange = "<colorRange>{$colorRange}</colorRange>";
		return $colorRange;
	}
	
	//固定1-40%范围
	public function _rangeColor1to10percent($max=100){

		$offer = 10;
		for($i=0; $i<5; $i++){
			$minValue = ($offer*$i);
			$colorRange .= "<color minValue='".($minValue)."' maxValue='".($offer*($i+1))."' displayValue='".($minValue?$minValue.'%':$minValue)."-".($offer*($i+1))."%' color='".$this->aGeoColor[$i]."' />";
		}
		//最后那条
		$colorRange .= "<color minValue='".($offer*$i)."' maxValue='".($max+1)."' displayValue='>".($offer*$i)."%' color='".$this->aGeoColor[$i]."' />";
		$colorRange = "<colorRange>{$colorRange}</colorRange>";
		return $colorRange;
	}
	
	//对一些国家表中没有代码,而flash中有的国家, 在此做兼容
	function _getCompatibleCode($aryLabelSet4Geo){
		$aryLabelSet4Geo['TW'] = \YcheukfReport\Lib\ALYS\ALYSLang::_('taiwan');	
		$aryLabelSet4Geo['CD'] = \YcheukfReport\Lib\ALYS\ALYSLang::_('CongoRepublic');	
//		$aryLabelSet4Geo['CG'] = $GLOBALS['lang']['taiwan'];	
		return $aryLabelSet4Geo;
	}

}
?>