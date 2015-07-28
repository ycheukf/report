<?php
/**
* List的PLUGIN
*
* 负责维度与指标的格式化
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Detail extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}


	/**
	 *	负责转换维度配置至所需要的格式

	 @param array a 当前核心类所处理的数据结构
	 @return array 处理过后的数据结构
	 */
	public function ALYSfmt_dimen($a){
		$aRe = array("title" => \YcheukfReport\Lib\ALYS\ALYSLang::_('DIMEN'));
		foreach($a as $aTmp){
			$aTmpData = array();
			$aTmpData['pName'] = $aTmp['key'];
			$aTmpData['label'] = \YcheukfReport\Lib\ALYS\ALYSLang::_($aTmp['key']);
			$aTmpData['options'] = array();
			$aTmpData['selected'] = $aTmp['selected'];
			foreach($aTmp['options'] as $key2){
				$aTmpData['options'][$key2] = \YcheukfReport\Lib\ALYS\ALYSLang::_($key2);
			}
			if($aTmp['group'] != false)//不需要group
				$aRe['data'][] = $aTmpData;
		}
		return $aRe;
	}

	/**
	 *	明细列表序号背景颜色

	 @return array 背景颜色数组
	 */
	public function ALYSgetListBgColor(){
		return array('ca9d50','bddef9','f8cb00','9dc800','ff9f54','00a8a8','de5c5c','b070b0','78a442','c2ba00','009fde');
	}

	/**
	 *	负责转换指标配置至所需要的格式

	 @param array a 当前核心类所处理的数据结构
	 @return array 处理过后的数据结构
	 */
	public function ALYSfmt_metric($a){
		$aRe = array("title" => \YcheukfReport\Lib\ALYS\ALYSLang::_('METRIC'));
		foreach($a as $aTmp){
			$aTmpData = array();
			$aTmpData['pName'] = $aTmp['key'];
			$aTmpData['label'] = \YcheukfReport\Lib\ALYS\ALYSLang::_($aTmp['key']);
			$aTmpData['type'] = 'radio';
			$aTmpData['options'] = array('none'=>\YcheukfReport\Lib\ALYS\ALYSLang::_('none'), $aTmp['key'] => \YcheukfReport\Lib\ALYS\ALYSLang::_($aTmp['key']));
			$aTmpData['selected'] = $aTmp['show']==true ? $aTmp['key'] : 'none';
//			if($aTmp['show'] != false)//不需要group
			$aRe['data'][] = $aTmpData;
		}
		return $aRe;
	}



	/**
	 *	明细列表html列表包装的定制
	*@return string HTML字符串
	 */
	public function ALYSfmt_list_table($th,$html){
		$th_pack = '';
		////表头开始////
		if($this->_isPerspective())
		{
			//透视图不加行标 在 ALYSfmt_list_title 里加
			$th_pack .= "\n<thead>";
			$th_pack .= $th;
			$th_pack .= "\n</thead>";
		}
		else
		{
			$th_pack .= "\n<thead>";
			$th_pack .= "\n<tr _listtrtitle>\n";
			$th_pack .= $th;
			$th_pack .= "\n</tr>";
			$th_pack .= "\n</thead>";
		}
		//////表头结束////
		$sContent = "\n<table _listtablecss0 >".$th_pack.$html."\n</table>";
		return $sContent;
	}

	/**
	 *	明细列表html列表头的定制
	*@param array groups 列表头数据
	*@return string HTML字符串
	 */
	public function ALYSfmt_list_title($groups,$format='html'){

		$bIsPerspective = $this->_isPerspective();

		//
		$aDimens = array();
		$aMetrics = array();
		$aMetricKeys = array();

		$html = "<th _listtdcss0 ><div>".\YcheukfReport\Lib\ALYS\ALYSLang::_('NO').('pdf'==$format?'':" <span _listThSpan _listspan0 >".\YcheukfReport\Lib\ALYS\ALYSLang::_('select')."</span>")."</div></th>";
		if(is_array($groups)){
			foreach($groups as $k => $aTmp){
				foreach($aTmp as $key=>$aGroup){

					$key = $aGroup['key'];
					$label = \YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']);
					if('dimen'==$k){
						//$label = \YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']);
						$tipLabel = "title='".\YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']."-tip")."'";
						$aDimens[] = $aGroup['key'];
					}else{
						//$label = ($bIsPerspective?$aGroup['label']:\YcheukfReport\Lib\ALYS\ALYSLang::_($aGroup['key']));
						$tipLabel = '';
						$aMetrics[$aGroup['label']] = $aGroup['label'];
						$aMetricKeys[$aGroup['key']] = $aGroup['key'];
					}
					$aThClass = isset($aGroup['thclass']) && count($aGroup['thclass']) ? $aGroup['thclass'] : array('td150');
					$aTipClass = isset($aGroup['tipclass']) && count($aGroup['tipclass']) ? $aGroup['tipclass'] : array('jp_tip');
					if($aGroup['sortAble'])
						$aThClass[] = 'sortable';
					if(isset($aGroup['orderbyClass']) && !empty($aGroup['orderbyClass']))
						$aThClass[] = $aGroup['orderbyClass'];
	//				var_export($aThClass);
					$sThClass = $sTipClass = "";

					//根据不同的输出类型处理样式
					switch($format){
						default:
						case 'html':
							$sTipClass = " class='".join(" ", $aTipClass)."'";
							$sThClass = " class='".join(" ", $aThClass)."'";
							break;
						case 'pdf'://pdf中需要把这些class分别替换
							foreach($aTipClass as $c){
								$sTipClass = " class='".$c."'";
							}
							foreach($aThClass as $c){
								$sThClass = " class='".$c."'";
							}
							break;
					}

					$html .= "\n<th nowrap _".$k."listthcss0 data-key='{$key}' {$sThClass}><div>".$label.
						"<span _".$k."listspan0  {$sTipClass} {$tipLabel}>&nbsp;</span></div></th>";

				}
			}
		}

		//拼凑透视表的头
		$return_html = "";
		$sPerspective = '<th _listtdcss0 >&nbsp;</th>';
		if($bIsPerspective)
		{
			$return_html = "\n<tr _listtrtitle>\n";
			$return_html .= $html;
			$return_html .= "\n</tr>";
			if(is_array($aDimens))
			{
				foreach($aDimens as $dv)
				{
					$sPerspective .="\n<th nowrap><div>&nbsp;<span >&nbsp;</span></div></th>";
				}
			}

			$iRowSpan = count($aMetricKeys);
			if(is_array($aMetrics))
			{
				foreach($aMetrics as $mv)
				{
					$sPerspective .="\n<th colspan='".$iRowSpan."'><div>&nbsp;<span >".$mv."</span></div></th>";
				}
			}
			$return_html = '<tr>'.$sPerspective.'</tr>'.$return_html;
		}
		else
		{
			$return_html .= $html;
		}

		return $return_html;
	}


	/**
	 *	明细列表html定制
	*@param array aryListData 列表数据
	*@return array 列表数据
	 */
	public function ALYSfmt_list($aryListData){
		$html = '';
//		print_r($aryListData);exit;
		//多个时段
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$iMultDataNum = count($aInput['date']);
		$iCnt = count($aryListData);
		for($i=0; $i<$iCnt; $i++){
			$trClass = isset($aryListData[$i]['dimens'][0]['trClass'])?$aryListData[$i]['dimens'][0]['trClass']:"";
			$strClass = $trClass==""?"":"class='{$trClass}'";
			$html .= "\n<tr ".($i%2==0?'_listbodytrcss0':'_listbodytrcss1')." {$strClass}  align='right'>";
			if(isset($aryListData[$i]) && is_array($aryListData[$i]))
			{
				foreach($aryListData[$i] as $key =>$aTmp){
					$defaultAlign = $key=='dimens'?"left":"right";
					$key	  = "";
//					var_export($aTmp);
					foreach($aTmp as $ii => $item){
						$sMetric = isset($item['metric']) ? $item['metric'] : "";
						for($iMDN=1;$iMDN<$iMultDataNum;$iMDN++){
							if(!empty($item['label'.$iMDN])){
								$item['label'] .= '<br/>'.$item['label'.$iMDN];
							}
						}
						if($key == 'dimens' && $ii == 0) {
							continue;
						}
						$colspan = isset($item['colspan'])?$item['colspan']:1;
						$rowspan = isset($item['rowspan'])?$item['rowspan']:1;
						if($colspan==0)continue;//被前面的td跨列
						$align = isset($item['align'])?$item['align']:$defaultAlign;
						$class = isset($item['className'])?$item['className']:'td150';
						$style = isset($item['style'])?$item['style']:'';
						if(!empty($item['tdClass']))
							$item['label'] = '<a href="javascript:void(0);" class="'.$item['tdClass'].'" data-key="'.$item['tdKey'].'"  val="'.$item['tdVal'].'">'.$item['label'].'</a>';
//						$key ="key='{$sMetric}' val='{$item['tdVal']}'" ;
						$key ="data-key='".$item['tdKey']."'  val='{$item['tdVal']}'" ;
						$labelLTag = isset($item['labelLTag'])?$item['labelLTag']:'';
						$labelRTag = isset($item['labelRTag'])?$item['labelRTag']:'';
						$label = isset($item['label'])?$labelLTag.$item['label'].$labelRTag:'';
						$sStyle = empty($style)?"":"style='{$style}'";
						$sAlign = empty($align)?"":"align='{$align}'";
						$sColspan = $colspan==1?"":"colspan='{$colspan}'";
						$sRowspan = $rowspan==1?"":"rowspan='{$rowspan}'";

						if(isset($item['tdTitle']))
							$sTitle = "title='".$item['tdTitle']."'";
						else
							$sTitle = !isset($item['rowspan']) ? "title='".(empty($sRowspan)?strip_tags($item['label']):"")."'" : "";

						$html .= "\n<td {$sAlign} {$sColspan} {$sRowspan} _listbodytdcss0 {$sTitle} {$key} >{$label}</td>";
					}
				}
			}
			$html .= "\n</tr>";
		}
		return $html;
	}



	//是否为透视图
	private function _isPerspective()
	{
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
		$sListType = $aInput['input']['detail']['type'];
		return 'perspective'==$sListType;
	}

}


/*

  "filterSelect":
 [
	{//维度或指标块
		"title":"\u7ef4\u5ea6",//块名称
		"data": //块配置
		[
			{
				"pName":"siteCodeDomain", //变量名
				"label":"\u57df\u540d\u5f52\u5e76\u7b49\u7ea7",
				"options":{"none":"none","siteCodeId":"\u8ddf\u8e2a\u4ee3\u7801\u6807\u8bc6","domainId":"\u4e3b\u57df"},//选项
				"selected":"siteCodeId"
			}


			 ,{"pName":"timeslot","label":"\u65e5\u671f","options":{"none":"none","timeslot":"\u65e5\u671f"},"type":"select","selected":"timeslot"},{"pName":"location","label":"\u5730\u57df","options":{"none":"none","country":"\u56fd\u5bb6","province":"\u56fd\u5bb6-\u7701\u4efd","city":"\u56fd\u5bb6-\u7701\u4efd-\u57ce\u5e02"},"type":"select","selected":"country"}
		]
	},
	*/
?>