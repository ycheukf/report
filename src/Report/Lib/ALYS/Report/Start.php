<?php
namespace YcheukfReport\Lib\ALYS\Report;
//use YcheukfReport\Lib\ALYS\Report\Advance;
/**
	@version V1.0 Nov 2011   (c) 2011-2012 (allyes.com). All rights reserved.
	报表通用类
 */
class Start{

	public static $aInput;
	public static $aInputDefault=null;//defaut aInput, used when aInput is not pass
	public static $aOutput;
	public static $paramKey;
	public static $_reportCachePrefix='userId_';



	public function __construct($aInput=''){

		self::$aInput = $aInput;
		self::$aInputDefault=array('some default params');


	}


	/**
	* get input var
	*/
	public static function getInput(){
		return Start::$aInput;
	}

	/**
	* get input var
	*/
	public static function getInputPageParams(){
		$aPage = isset(Start::$aInput['input']['detail']['page']) ? Start::$aInput['input']['detail']['page'] : array();
		$aPage['items_per_page'] = isset($aPage['items_per_page']) ? (int)$aPage['items_per_page'] : 5;
		$aPage['current_page'] = isset($aPage['current_page']) ? (int)$aPage['current_page'] : 0;
		$aPage['total'] = isset($aPage['total']) ? (int)$aPage['total'] : 0;
		return $aPage;
	}

	/**
	* get input var
	*/
	public static function setInput($a){
		self::$aInput = $a;
		return self::$aInput;
	}

	/**
	* get output var
	*/
	public static function getOutput(){
		return self::$aOutput;
	}

	/**
	* get output var
	*/
	public static function setOutput($a){
		self::$aOutput = $a;
		return self::$aOutput;
	}

	/**
	* set param Key
	*/
	function setParamKey($s){
		self::$paramKey=$s;
	}

	/**
	* save to cache aConfig ,return false/true;
	*/
	function _cacheLastParam(){
		$key	= empty(self::$paramKey) ? false : self::$paramKey;
		$config	= empty(self::$aInput)	? false : self::$aInput;
		$cache	=	\YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("cache");
		if($key && $config)
		{
			$cache->save($this->_getCacheKey($key), serialize($config));			// array=>serialize
		}
	}

	function _getCacheKey($key){
		return self::$_reportCachePrefix.$key;
	}
	/**
	* get to cache aConfig,return arr/null
	*/
	function _getLastParam(){
		$cacheBuffer = array();
		$cache	=	\YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("cache");
		$key	=	empty(self::$paramKey) ? false : self::$paramKey;
		if( $key )
		{
			$cacheBuffer=unserialize($cache->get($this->_getCacheKey($key)));//serialize=>array
		}
		return empty($cacheBuffer) ? false : $cacheBuffer;
	}
	/**
	* check  cache aConfig
	*/
	function _chkLastParam(){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$isCache = isset($_ALYSconfig['report']['saveLastParam']) ? $_ALYSconfig['report']['saveLastParam'] : false;
		if(empty(self::$aInput)) // When Inparam is empty
		{
			self::$aInput = self::$aInputDefault;
			if($isCache)
			{
				if(!self::$aInput = $this->_getLastParam())
				{
					self::$aInput = self::$aInputDefault;
				}
			}
		}else // When Inparam is not empty
		{
			if($isCache)
			{
				$this->_cacheLastParam();
			}
		}
	}
	/**
	* get engine name
	*/
	function _enginName($s){
		return 'o'.\YcheukfReport\Lib\ALYS\ALYSFunction::ucfirst($s);
	}

	/**
	* load engine
	*/
	private function _loadEngine($p){
		$aInput = self::getInput();
		$sEngDir = \YcheukfReport\Lib\ALYS\ALYSFunction::ucfirst($p);
		$sEngFile = \YcheukfReport\Lib\ALYS\ALYSFunction::ucfirst($aInput['input'][$p]['type']);

		//输入参数转化
		$oInput = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.input.{$sEngDir}.{$sEngFile}");
		$oInput->fmtInput();

		\YcheukfReport\Lib\ALYS\ALYSFunction::debug($aInput,'a',"input data after fmt:report.engine.{$sEngDir}.{$sEngFile}");

		//处理
		$sObjectEng = $this->_enginName($sEngDir);
		$this->$sObjectEng = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.engine.{$sEngDir}.{$sEngFile}");
		self::$aOutput[$p]=$this->$sObjectEng->getData();

		\YcheukfReport\Lib\ALYS\ALYSFunction::debug(self::$aOutput,'a',"data after engine: report.engine.{$sEngDir}.{$sEngFile}");

		//输出参数转化
		$oOutput = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.{$sEngDir}.{$sEngFile}");
		$oOutput->fmtOutput();

		\YcheukfReport\Lib\ALYS\ALYSFunction::debug(self::$aOutput,'a',"data after output: report.engine.{$sEngDir}.{$sEngFile}");
	}
	/**
	* 初始化公用参数
	*/
	public function initParam(){
	}

	/**
	* 初始化参数的顺序, 优先级为 total>list>flash
	*/
	public function initInputSequence(){
		$aInput = self::getInput();
		$aInputNew = $aInput;
		if(isset($aInput['input']['total'])){//将total放于第一位以适应 list.table_pie 情况
			unset($aInputNew['input']['total']);
			$aInputNew['input']['total'] = $aInput['input']['total'];
		}
		if(isset($aInput['input']['detail']) && isset($aInput['input']['flash'])){//调整list,flash顺序以适应sort类型的组合列表
			unset($aInputNew['input']['detail']);
			unset($aInputNew['input']['flash']);
			$aInputNew['input']['detail'] = $aInput['input']['detail'];
			$aInputNew['input']['flash'] = $aInput['input']['flash'];
		}
		$aInput = $aInputNew;
		self::setInput($aInput);
	}


	/**
	* 入口
	*/
	public function go(){
		$aInput = self::getInput();

		//判断格式是否为全部导出
		$oExportAll = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass('report.exportall', $aInput);
//		var_dump($oExportAll);
		if($oExportAll->isExportAll()){
			return $oExportAll->exportAllData();
		}
		$this-> _checkInputParam();
		$this->initParam();
		$this->_chkLastParam(); //add check cache aConfig

		//为饼图添加,计算总和 只有列表时设置total
		if(isset($aInput['input']['detail']) && (!isset($aInput['input']['total']) or empty($aInput['input']['total']))){
			$aInput['input']['total'] = $this->_getTotalInput($aInput['input']);
		}
		$this->initInputSequence();
		//return $aInput;
		foreach($aInput['input'] as $p=>$inputType){
			if(count($inputType) >0){
				$this->_loadEngine($p);

			}
		}
		return $this->_output();
	}

	public function _output(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("Report.Output.format");
		$o->go();
		$aRe = self::$aOutput;
		if(isset($aRe['flash']))
			unset($aRe['flash']);
		if(isset($aRe['total']))
			unset($aRe['total']);
		if(isset($aRe['detail']))
			unset($aRe['detail']);
		return $aRe;
	}


	/**
	* check common input param
	*/
	public function _checkInputParam(){
		$aInput = self::getInput();
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$aInputkey=array('dbHandle','date','filters','input','output','custom','groups','nodateFlag');
		if(!is_array($aInput))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG',json_encode($aInput));
		foreach($aInput as $key=>$val){
			if(!in_array($key,$aInputkey)){
				throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG',"::".$key);
			}
		}
		$aInput['dbHandle'] = isset($aInput['dbHandle']) ? $aInput['dbHandle'] : 'Zend\Db\Adapter\Adapter';
		$aInput['filters'] = isset($aInput['filters']) ? $aInput['filters'] : array();
		if(!isset($aInput['dbHandle'])){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_VALUE_WRONG','dbHandle');
		}


		//兼容没有date的情况
		$aInput['nodateFlag'] = false;
		if(!isset($aInput['date']) || empty($aInput['date'])){
			$aInput['nodateFlag'] = true;
			$aInput['date'] = array(
				array(
					's'=>'1979-01-01',
					'e'=>'1979-01-01',
				)
			);
		}
		$aDateKey=array('s','e');
		$dateReg="/\d{4}-\d{2}-\d{2}/";
		foreach($aInput['date'] as $Date){
			$DateKey=array_keys($Date);
			foreach($DateKey as $key){
				if(!in_array($key,$aDateKey)){
					throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','date');
				}
				if(!preg_match($dateReg,$Date[$key])){
					throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_VALUE_WRONG','date');
				}

			}
		}
		$aFiltersKey=array('key','op','value', 'group');
		foreach($aInput['filters'] as $Filters){
			$FiltersKey=array_keys($Filters);
			foreach($FiltersKey as $key){
				if(!in_array($key,$aFiltersKey)){
					throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','filters');
				}
			}
		}

		foreach($aInput['input'] as $p=>$inputType){
			if(!in_array($p,array('flash','total','detail'))){
				throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_KEY_WRONG',$p);
			}
		}
		self::setInput($aInput);
	}


	/**
	* 获取所有Dimen
	*/
	public function getDimens($type='detail'){
		$aRe = array();
		$aInput = self::getInput();
		if(!is_array($aInput['input'][$type]['table']))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_NO_'.$type.'_DIMEN',$p);

		foreach($aInput['input'][$type]['table'] as $table => $aTmp){
			$aRe = array_merge($aRe, $aTmp['dimen']);
//			$aRe = $aRe + $aTmp['dimen'];
		}
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin($type);
		$aRe = $oPlugin->ALYSfmt_dimen($aRe);

		return $aRe;
	}

	/**
	* 获取所有Metric
	*/
	public function getMetrics($type='detail'){
		$aInput = self::getInput();
		if(!is_array($aInput['input'][$type]['table']))
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_INPUT_NO_'.$type.'_DIMEN',$p);

		$aRe = array();
		foreach($aInput['input'][$type]['table'] as $table => $aTmp){
			$aRe = array_merge($aRe, $aTmp['metric']);
		}
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin($type);
		$aRe = $oPlugin->ALYSfmt_metric($aRe);
		return $aRe;
	}

	/**
	* 为table_pie, table_bar加入total的input
	*/
	private function _getTotalInput($aInput){
		$aRe = array();
		$tableType = $aInput['detail']['type'];
		if($tableType == 'table_pie' or $tableType == 'table_bar'){
			if(\YcheukfReport\Lib\ALYS\Report\Advance::isAdvanced('detail')){//自定义数据
				$aRe['advanced'] = array(
					'type' => 'datas',
					'dimen' => $aInput['detail']['advanced']['dimen'],
					'metric' => $aInput['detail']['advanced']['metric'],
					'data' => $aInput['detail']['advanced']['total'],
				);
			}else{
				$aRe['table'] = $aInput['detail']['table'];
			}
			$aRe['type'] = 'common';

		}
		return $aRe;
	}

	/**
	* 获取所有Metric
	*/
	public function getDimenMetric($type='detail'){
		return array($this->getDimens($type), $this->getMetrics($type));
	}
}
?>