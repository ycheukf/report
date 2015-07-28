<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Detail;
class Perspective extends \YcheukfReport\Lib\ALYS\Report\Output\Detail{

	private $_aDimenOld;

	public function __construct(){
		parent::__construct();

	}


	/**
	* 格式化成维度与指标分开
	*/
	public function _fmtDimen_Metric($type){

		$this->aOutput[$type] = $this->_fmtTdStyle($type);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}


	public function fmtOutput()
	{
		$type='detail';
		//初始化dimen metric
		$this->_initDimen_Metric($type);

		//纯数据保存
		$this->aInput['internal']['listData']=$this->aOutput[$type];
		$this->aInput['internal']['xdimen']=$this->_getXField();
		//格式化 metric的值
		$this->_formatMetric($type);

		//重组 dimen & metric 并补全数据
		$this->_recomDimenMetric();

		//之前没数据 重组后可能有数据了
		if(empty($this->aInput['internal']['listData']))
		{
			$this->aInput['internal']['listData']=$this->aOutput[$type];
		}

		//格式化 dimen的值
		$this->_chgId2Lable($type);

		$this->_fmtOutput($type);
	}


	private function _getMetric(){
		$aInput = $this->aInput['input'][$this->_type];
		$aMetric = array();
		if(is_array($aInput['tables'])){
			foreach($aInput['tables'] as $table => $tv){
				$aMetric = array_merge($aMetric,$tv['metric']);
			}
		}
		return $aMetric;
	}


	public function _recomDimenMetric()
	{
		//格式化透视数据 使之适应通常列表格式 补全
		$this -> _fmtListData();

		//清除xdimen
		$aInput = $this->aInput['input'][$this->_type];
		$mainTable=$aInput['mainTable']['table'];
		$xDimen = @$aInput['table'][$mainTable]['xdimen_key'][0];
		$this->_aDimenOld = $this->aDimen;
		$this->aDimen = $aInput['table'][$mainTable]['ydimen_key'];

		$arrYDimenSelected = $aInput['table'][$mainTable]['ydimen_key_select'];

		//保持标题列和表体列一致
		if(is_array($this->aInput['groups']['dimen']))
		{
			foreach($this->aInput['groups']['dimen'] as $k => $v)
			{
				if(!in_array($k,$arrYDimenSelected))
				{
					unset($this->aInput['groups']['dimen'][$k]);
				}
			}
		}

	}


	private function _chgId2LabelForXField($aXDimen,$aXField)
	{
		$oId2Label = \YcheukfReport\Lib\ALYS\ALYSFunction::loadDictionary('Id2label');
		return $oId2Label->ALYSchgId2Label(array($aXDimen),array($aXDimen=>$aXField));
	}


	private function _splitAppend($arrAppend)
	{
		return implode($this->splitChar,$arrAppend);
	}


	private function _getXField()
	{
		return $this->aInput['input'][$this->_type]['dimenpage']['xfield'];
	}


	private function _getMetricInGroup()
	{
		return $this->aInput['groups']['metric'];
	}


	private function _setMetricInGroup($aNewMetricInGroup)
	{
		if(!empty($aNewMetricInGroup))return $this->aInput['groups']['metric']=$aNewMetricInGroup;
	}


	private function _fmtListData()
	{
		$iDefaultMetricValue = 0;//无值时的默认值

		$aInput = $this->aInput['input'][$this->_type];
		$mainTable=$aInput['mainTable']['table'];

		//维度key
		$xDimen = @$aInput['table'][$mainTable]['xdimen_key'][0];
		$yDimens = $aInput['table'][$mainTable]['ydimen_key'];
		//dimen与xdimen是否有重复
		$boolIsDimenHasXDimen = in_array($xDimen,$yDimens)?true:false;

		//维度 数据
		$aXField = $this->_getXField();
		$aYField = $this->aInput['input'][$this->_type]['dimenpage']['yfield'];

		$aXFieldLabelRet = $this->_chgId2LabelForXField($xDimen,$aXField);
		$aXFieldLabel = $aXFieldLabelRet[$xDimen];

		$aMetricInGroup=$this->_getMetricInGroup();

		$data = $this->aOutput[$this->_type][0];//单时段 所以用0
		$return_data = array();
		$arrDimenData = array();
		$aMetric = array();
		$aNewMetricInGroup=array();
		//var_dump($data);//exit;
		if(is_array($aYField))
		{
			foreach($aYField as $yF)
			{
				$sKey = $this->_splitAppend($yF);
				$return_data[$sKey]=$yF;

				if(is_array($aXField))
				{
					foreach($aXField as $xF)
					{

						$sKeySplit = $boolIsDimenHasXDimen?$sKey:($this->_splitAppend($yF+array($xF)));

						if(is_array($this->aMetric))
						{
							foreach($this->aMetric as $mk)
							{
								$sMKey = $this->_splitAppend(array($xF,$mk));
								if(!isset($aMetric[$sMKey]))$aMetric[$sMKey] = $sMKey;
								if(!isset($aNewMetricInGroup[$sMKey]))
								{
									$aNewMetricInGroup[$sMKey]=$aMetricInGroup[$mk];
									$aNewMetricInGroup[$sMKey]['label']=isset($aXFieldLabel[$xF])?$aXFieldLabel[$xF]:'';
								}

								if(isset($data[$sKeySplit])&&(($boolIsDimenHasXDimen&&$data[$sKeySplit][$xDimen]==$xF)||!$boolIsDimenHasXDimen))
								{
									$return_data[$sKey][$sMKey] = $data[$sKeySplit][$mk];
								}
								else
								{
									$return_data[$sKey][$sMKey] = $iDefaultMetricValue;
								}
							}
						}
					}
				}

			}

		}
//var_dump($return_data);//exit;
		//重组metric
		if(!empty($aMetric))$this->aMetric=$aMetric;
		$this->_setMetricInGroup($aNewMetricInGroup);

		//数据赋值
		$this->aOutput['detail']=array($return_data);

		//分页用的数据
		$iYtotal = $aInput['page']['total'];
		$iXtotal = $aInput['dimenpage']['total'];
		//每页条数
		$iYperpage = $aInput['page']['items_per_page'];
		$iXperpage = $aInput['dimenpage']['items_per_page'];
		$this->aOutput['detail.page'] = array('total'=>$iYtotal,'items_per_page'=>$iYperpage);
		$this->aOutput['detail.dimenpage'] = array('total'=>$iXtotal,'items_per_page'=>$iXperpage);
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($this->aOutput);
	}


	public function _fmtOutput(){
		$type='detail';
		$this->_fmtDimen_Metric($type);

		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.".$type.".format.".$this->aInput['output']['format']);
		$o->go();

	}

}