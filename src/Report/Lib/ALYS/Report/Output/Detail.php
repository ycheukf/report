<?php
namespace YcheukfReport\Lib\ALYS\Report\Output;
class Detail extends \YcheukfReport\Lib\ALYS\Report\Output{

	protected $_type = 'detail';

	public function __construct(){
		parent::__construct();
		$this->sThLabel = 'totalPercent';//指标百分比的label
	}

	/**
	* 为表格的TD组织数组
	*/
	public function _fmtTdStyle($type){
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin('detail');
		$aBgColor = $oPlugin->ALYSgetListBgColor();

		$bNOColorFlag = 1;//用来控制列表中是否出现色块的bool
		switch($this->aInput['input']['detail']['type']){
			case 'table_pie':
				$bNOColorFlag = 1;
				break;
			default:
				$bNOColorFlag = 0;
				break;
		}
		$aOrgData=array();
		$TablePieThSpanFlag = 0;

		$aPureData = $this->aInput['internal']['listData'];//纯数据 未格式化的
		$aOrgData = $this->aOutput[$type];//通过format的数据

		//适应多时段情况
		$iDataCnt = 0;//表示最多数据条数 后边作循环用
		$iMultNum = count($aOrgData);//表示一共显示多少个时段
		foreach($aOrgData as $k => $v){
			$iVcnt = count($v);
			$iDataCnt = $iVcnt>$iDataCnt?$iVcnt:$iDataCnt;
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					$v[$k1]['concatKey'] = $k1;
				}
			}
			$aOrgData[$k] = array_values($v);//索引数字化
			$aPureData[$k] = array_values($aPureData[$k]);
		}
		$aDatas = array();
		for($i=0;$i<$iDataCnt;$i++){
			$index_i_t=$i+1;
			if(isset($aBgColor[$i])){
				$BgColor=$aBgColor[$i];
			}else{
				$BgColor=$aBgColor[10];
			}
			$sStyleTmp = $bNOColorFlag ? 'style=\'background-color: #' .$BgColor. '\'' : '';
			$aDatas[$i]['dimens'][0]=array(
				'label' =>"<b _listbodybcss ><b {$sStyleTmp}>&nbsp;</b>". $index_i_t ."</b>",
				'className' => 'fline',
				'align' => 'center',
				'tdKey' => 'tdNO',
				'tdVal' => $index_i_t,
				//'trClass' => 'report_table_tr_0',
			);
			$trClass = ($aDatas[$i]['dimens'][0]['tdVal'])%2? 1 : 2;
			if($trClass == 1){
				$aDatas[$i]['dimens'][0]['trClass'] = 'report_table_tr_0';
			}else{
				$aDatas[$i]['dimens'][0]['trClass'] = 'report_table_tr_1';
			}
			foreach($this->aDimen as $l=>$dimen){
				$l_t=$l;
				$l++;
				$aDatas[$i]['dimens'][$l]=array(
					'className' => 'td150',
					'labelLTag' => '',
					'labelRTag' => '',
					'tdClass' => '',
					'tdKey' => $dimen,
					//'tdVal' => $aConcat[$l_t],
				);
				for($mn=0;$mn< $iMultNum;$mn++){
					$aConcat=explode($this->splitChar,$aOrgData[$mn][$i]['concatKey']);//var_dump($aConcat);exit;
					$aDatas[$i]['dimens'][$l]['label'.($mn?$mn:'')]=$aOrgData[$mn][$i][$dimen];
					$aDatas[$i]['dimens'][$l]['tdVal']=$aConcat[$l_t];
				}
			}

			foreach($this->aMetric as $m=>$metric){
				//$iPureVal =
				if($this->orderby==$metric){//排序
					if($i % 2==0){
						$className_t='current_odd td150';
					}else{
						$className_t='current_even td150';
					}
					$aDatas[$i]['guideLines'][$m]=array(
						'className' => $className_t,
					);
					for($mn=0;$mn<$iMultNum;$mn++){
						$aDatas[$i]['guideLines'][$m]['label'.($mn?$mn:'')]=@$aOrgData[$mn][$i][$metric];
						$aDatas[$i]['guideLines'][$m]['tdVal'.($mn?$mn:'')]=@$aPureData[$mn][$i][$metric];
					}
				}else{
					switch($metric){
						case $this->sThLabel://饼图,柱状图
							switch($this->aInput['input']['detail']['type']){
								case 'table_pie'://饼图
									if($TablePieThSpanFlag == 0){
										$aDatas[$i]['guideLines'][$m]=array(
											'rowspan' => $iDataCnt,
											'label' => @$aOrgData[0][$i][$metric],
										);
										$TablePieThSpanFlag = 1;
									}else{
										$aDatas[$i]['guideLines'][$m]['colspan']=0;
									}
									break;
								case 'table_list'://柱状图
								default:
									for($mn=0;$mn<$iMultNum;$mn++){
										$aDatas[$i]['guideLines'][$m]['label'.($mn?$mn:'')]=@$aOrgData[$mn][$i][$metric];
										$aDatas[$i]['guideLines'][$m]['tdVal'.($mn?$mn:'')]=@$aPureData[$mn][$i][$metric];
									}
									break;
							}

						break;
						default:
							for($mn=0;$mn<$iMultNum;$mn++){
								$aDatas[$i]['guideLines'][$m]['label'.($mn?$mn:'')]=@$aOrgData[$mn][$i][$metric];
								$aDatas[$i]['guideLines'][$m]['tdVal'.($mn?$mn:'')]=@$aPureData[$mn][$i][$metric];
							}
						break;

					}
				}
				$aDatas[$i]['guideLines'][$m]['metric']=$metric;
			}

		}
//		var_export($this->aMetric);
//		var_export($aDatas);
		return $aDatas;
	}

	/**
	* 重写output.php
	*/
	public function fmtOutput(){

		$type='detail';
		$this->_initDimen_Metric($type);
		$this->aInput['internal']['listData']=$this->aOutput[$type];
		//print_r($this->aOutput[$type]);exit;
		$this->_chgId2Lable($type);

		//print_r($this->aOutput[$type]);
		$this->_formatMetric($type);

		//print_r($this->aOutput[$type]);
		//print_r($this->aInput['input'][$type]['page']);

		$this->_fmtOutput($type);
		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);
	}

	protected function _getPercent(){
		$type = 'detail';
		$sSelectedMetric = $this->aInput['input']['detail']['selected'];

		if(!isset($sSelectedMetric)){  //没有设置百分比选项,使用默认
			$keys = array_keys($this->aInput['groups']['metric']);
			$sSelectedMetric = $keys[0];
		}

		//计算总和
		$sum = $this->aOutput['total'][0][0][$sSelectedMetric];
		//组织伪指标中的内容
		foreach($this->aOutput[$type] as $k => $v){
			foreach($v as $kk => $vv){
				$percent = round((($vv[$sSelectedMetric]/$sum)*100),2)."%";
				$this->aOutput[$type][$k][$kk][$this->sThLabel] = $percent;
			}
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);

		//为th增加一列伪指标:百分比
		array_push($this->aMetric, $this->sThLabel);
//		var_export($this->aMetric);
	}

	/**
	* ID 转化为label
	*/
	public function _chgId2Lable($type){
		$aID=array();
		foreach($this->aOutput[$type] as $date_i => $aData){
			foreach($aData as $Data){
				foreach($this->aDimen as $dimen){
					$aID[$dimen][]=$Data[$dimen];
				}
			}
		}

		$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
		$aLabel = $oId2Label->ALYSchgId2Label($this->dimenkey2selected,$aID);

		foreach($this->aOutput[$type] as $date_i => $aData){
			foreach($aData as $concatKey=>$Data){
				foreach($this->aDimen as  $dimen){
					$this->aOutput[$type][$date_i][$concatKey][$dimen]=$aLabel[$dimen][(string)$Data[$dimen]];
				}
			}
		}
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}


	/**
	* 例子数据
	* 将数据处理成适合html的格式
	*/
	function _fmtData(){
		return $this->_demo();
	}






}
?>
