<?php
namespace YcheukfReport\Lib\ALYS\Report\Dictionary;
/**
* id转label类
*
* 负责将db类中获得id转变成相应的label
*
* @author   ycheukf@gmail.com
* @package  Dictionary
* @access   public
*/
class Id2label extends \YcheukfReport\Lib\ALYS\Report\Dictionary{
	public $DB;
	public $lang='CN';
	public $combinationFieldSplitChar = '__combine__';

	public function __construct(){
//		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$this->DB = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("Dboperate.Sql");
	}

	
	/**
	 * 将数据表中获取得的相关ID转换成对应的文字

	* @param array aDimenkey2selected 存储需要转换的pName, array('timeslot', 'domainId', ...)
	* @param array aryIdSet 存储需要转换的pName与id序列, array('timeslot'=>array('2012-10-10', '2012-10-12'), 'domainId'=>array(1, 2, 3), ...)
	* @param array aConfig 将要输出的html
	* @return array 处理过后的html
	* @description 将数据表中获取得的相关ID转换成对应的文字 
	*
	*/
	public function ALYSchgId2Label($aDimenkey2selected=array(), $aryIdSet=array(), $aConfig=array()){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		define('__DEBUG__',$_ALYSconfig['debug']);


		$aryReturn = array();
		$aryTmp = array();
		if(empty($aryIdSet))return $aryReturn;
//		var_export($aDimenkey2selected);
		foreach($aDimenkey2selected as $dimenKey=>$sSelected)
		{
			if(is_int($dimenKey))
				$dimenKey = $sSelected;

			if(empty($aryIdSet[$dimenKey]))
				$aryIdSet[$dimenKey] = array();
			if(preg_match('/'.addslashes($this->combinationFieldSplitChar).'/', $dimenKey)){
				$a4Combin = $this->getCombinationFieldAry($dimenKey);
				$dimenKey = $a4Combin['name'];
			}
			switch($dimenKey)	//$dimenKey 为在数据表中的字段名
			{
				case 'none':
					break;
				case 'dbconfigop':		
					
					foreach($aryIdSet[$dimenKey] as $v){
						$aryReturn[$dimenKey][$v] = "<a _val='".$v."' class='edit' href='javascript::void()'>".\YcheukfReport\Lib\ALYS\ALYSLang::_('RE_DIMEN_EDIT')."</a> &nbsp; <a _val='".$v."' class='upgrade' href='javascript::void()'>".\YcheukfReport\Lib\ALYS\ALYSLang::_('RE_DIMEN_UPGRADE')."</a> &nbsp; <a _val='".$v."' class='downgrade' href='javascript::void()'>".\YcheukfReport\Lib\ALYS\ALYSLang::_('RE_DIMEN_DOWNGRADE')."</a>&nbsp; <a _val='".$v."' class='config' href='javascript::void()'>".\YcheukfReport\Lib\ALYS\ALYSLang::_('RE_DIMEN_CONFIG')."</a>";
					}
					break;
				case 'domainId':		
					$idStr = "'".implode("', '", array_unique($aryIdSet[$dimenKey]))."'";
					$aryTmp = $this->_getDomainListNameList('`DomainList`.ID in ('.$idStr.')');
					for($ii=0 ; $ii<count($aryTmp) ; $ii++)
					{
						extract($aryTmp[$ii]);
						$aryReturn[$dimenKey][$domainId] = htmlspecialchars((__DEBUG__?$domainId.',':'').$domain."(".$advName.")");
					}
					break;

					
				case 'advertiserId':
					$idStr = "'".implode("', '", array_unique($aryIdSet[$dimenKey]))."'";
					$aryTmp = $this->_getAdverNameList('ID in ('.$idStr.')');
					for($ii=0 ; $ii<count($aryTmp) ; $ii++)
					{
						extract($aryTmp[$ii]);
						$aryReturn[$dimenKey][$ID] = htmlspecialchars((__DEBUG__?$ID.',':'').$Name);
					}
					break;
				case 'channelGroupId':
					$aTmpSid = $aTmpCgId = array(-1);
					$aS2Name = $aCg2Name = array();
					for($ii=0 ; $ii<count($aryIdSet[$dimenKey]) ; $ii++)
					{
						list($aTmpSid[], $aTmpCgId[]) = explode($this->resourceSplitChar, $aryIdSet[$dimenKey][$ii]);
					}
					$sidStr = implode(", ", array_unique($aTmpSid));
					$cgidStr = implode(", ", array_unique($aTmpCgId));

					$aryTmp = $this->_getSiteNameList('ID in ('.$sidStr.')');
					for($ii=0 ; $ii<count($aryTmp) ; $ii++)
					{
						$aS2Name[$aryTmp[$ii]['ID']] = $aryTmp[$ii]['Sitename'];
					}
					$aryTmp = $this->_getChannelGroupNameList('ID in ('.$cgidStr.')');
					for($ii=0 ; $ii<count($aryTmp) ; $ii++)
					{
						$aCg2Name[$aryTmp[$ii]['ID']] = $aryTmp[$ii]['pactCGName'];
					}
					for($ii=0 ; $ii<count($aryIdSet[$dimenKey]) ; $ii++)
					{
						list($sSId, $sCgId) = explode($this->resourceSplitChar, $aryIdSet[$dimenKey][$ii]);
						$sSName = isset($aS2Name[$sSId]) ? $aS2Name[$sSId] :$this->_('fieldNotSet');
						$sCgName = isset($aCg2Name[$sCgId]) ? $aCg2Name[$sCgId] :$this->_('fieldNotSet');
						$aryReturn[$dimenKey][$aryIdSet[$dimenKey][$ii]] = $sSName.'/'.$sCgName;
					}
					break;

				case 'location':
					switch($sSelected){
						case 'country':
						case 'sony_location':
						case 'sony_city':
						case 'province':
						case 'city':
						default:
							$idStr = "'".implode("','", array_unique($aryIdSet[$dimenKey]))."'";
							$aConfig['countryNameFlag'] = !isset($aConfig['countryNameFlag'])?1:$aConfig['countryNameFlag'];
							$aryTmp = $this->_getLocationNameList($sSelected, $idStr, $aConfig);
							for($ii=0 ; $ii<count($aryTmp) ; $ii++)
							{
								extract($aryTmp[$ii]);
								if(!empty($country))
									$aryReturn[$dimenKey][$country] = (__DEBUG__?$country.'/':'').$name_country;
								if(!empty($province))
									$aryReturn[$dimenKey][$province] = (__DEBUG__?$province.'/':'').$name_province;
								if(!empty($city))
									$aryReturn[$dimenKey][$city] = (__DEBUG__?$city.'/':'').$name_city;
							}
//							var_export($sSelected);
//							var_export($aryReturn[$dimenKey]);
						break;
				
					}
					break;
				case 'fansPropertyKey':
					$aryReturn[$dimenKey] = array('f'=>'女','m'=>'男','n'=>'未知');
				break;
				case 'timeslot2':
					for($ii=0 ; $ii<count($aryIdSet[$dimenKey]) ; $ii++)
					{
						$aryReturn[$dimenKey][$aryIdSet[$dimenKey][$ii]] = "AA";
					}
				break;
				case 'num2citykey':
//					var_export(11);
					//var_dump($aryIdSet[$dimenKey]);exit;
					//根据$aryIdSet[$dimenKey]取得城市代码的映射
					$tmp_arr = array(
						//'58' => 'CNBJ',
					);
					foreach($tmp_arr as $k => $v){
						$aryReturn[$dimenKey][$v] = $k;
					}
				break;
				default:
					switch($sSelected){
						case 'country':
						case 'sony_location':
						case 'sony_city':
						case 'province':
						case 'city':
						default:
							foreach($aryIdSet[$dimenKey] as $v){
								$aryReturn[$dimenKey][(string)$v]=$v;
							}
//							var_export($sSelected);
//							var_export($aryReturn[$dimenKey]);
						break;
				
					}
					break;
			}
		}
		return $aryReturn;
	}



}
?>