<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Exportall extends \YcheukfReport\Lib\ALYS\Report{

	const MAX_DATA_COUNT = 5000;//定义每页最大输出数据条数
	const MAX_DATA_PAGE = 500;//定义最大输出页码数

	private $_aTypeAll = array('csv_all');
	private $_aInput;

	function __construct($inputArr=array()){
		$this->_aInput = $inputArr;
	}

	/**
	*	生成文件 供下载
	*
	*/
	public function exportAllData(){
		$this->chkAndParseInput();
		//最大执行时间设置为无限长
		set_time_limit(0);

		$csvLoading = false;
		$exportType = $this->_aInput['output']['exportInfo']['type'];

		$exportProperty = array();

		$fileName = @$this -> _aInput['output']['exportInfo']['fileName'];
		switch($exportType){
			case 'file':
				$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
				$fileDir = @$_ALYSconfig['cache']['csv']['cacheDir'];
				if(!is_dir($fileDir)){
					$ret = mkdir($fileDir,777, true);
					if(!$ret){
						throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','make dir fail:'.$fileDir);
					}
				}

				$csvLoading = @$this -> _aInput['output']['csvLoading'];//进度信息输出

				$fileName = rtrim($fileDir,'/').'/'.$this->_conv($fileName).'.csv';
				//先删除文件
				if(file_exists($fileName)){
					@unlink($fileName);
				}
				$exportProperty['fileName'] = $fileName;
			break;
			case 'download':
			default:
				ob_clean();
				//no cache
				header('Expires: ' . date(DATE_RFC1123));
				header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0');
				header('Pragma: no-cache');
				header('Last-Modified: ' . date(DATE_RFC1123));
				header('Pragma: public');

				$fileName = str_replace(array(';', '"', "\n", "\r"), '-', $fileName);
				$fileName = $this->_conv($fileName).'.csv';
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename="'.($fileName).'"');
				header('Content-Type: text/comma-separated-values');
				header('Content-Transfer-Encoding: binary');
				//header("Content-Length: ".$len);
			break;

		}

		//flash & total
		$flash_and_total = $this->_getFlsTotal();
		if(false!==$flash_and_total){
			$flash_and_total = $this->_conv($flash_and_total);
			$this -> _processData($flash_and_total,$exportType,$exportProperty);
		}

		$this -> _aInput['input']['detail']['page']['items_per_page'] = self::MAX_DATA_COUNT;//每页条数
		$doWhile = true;
		$iCurrentPage = 0;

		$sAtt = '';
		for($i=0;$i<1020;$i++){
			$sAtt .= " ";
		}
		$total_start = microtime(true);

		if($csvLoading){
			ob_start();
			ob_end_flush();
			echo "running...<br/>".$sAtt."\n";

			ob_flush();
			flush();
		}

		while($doWhile){
			$this -> _aInput['input']['detail']['page']['current_page'] = $iCurrentPage++;
			$start = microtime(true);
			$ret = $this->_getData();
			if($csvLoading){
				echo 'page '.$iCurrentPage.' spends time(sec):'.(round(microtime(true)-$start,2))."\n<br/>";
				ob_flush();
				flush();
			}

			//转成GBK输出
			$ret['data'] = $this->_conv($ret['data']);

			$this -> _processData($ret['data'],$exportType,$exportProperty);

			$this -> _aInput['input']['detail']['page']['startItem'] = $iCurrentPage*(self::MAX_DATA_COUNT);

			if($ret['num']<self::MAX_DATA_COUNT||$iCurrentPage>=self::MAX_DATA_PAGE)$doWhile=false;
		}

		if($csvLoading){
			echo 'total time:'.(round(microtime(true)-$total_start,2));
		}
		return true;
	}

	/**
	*	检查并重组输入的数组 只支持列表形式的导出
	*
	*/
	public function chkAndParseInput(){
		//只有列表形式能全部导出
		if(empty($this->_aInput['input']['detail'])||count($this->_aInput['input']['detail'])<=0){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','only list type can export');
		}
		if(empty($this->_aInput['output']['exportInfo']['type'])){
			$this->_aInput['output']['exportInfo']['type'] = 'download';
		}
		//导出类型判断
		if(!in_array($this->_aInput['output']['exportInfo']['type'],array('file','download'))){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','wrong type [output][exportInfo][type]');
		}
		//文件名是否有传入
		if(empty($this->_aInput['output']['exportInfo']['fileName'])){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','please set param [output][exportInfo][fileName]');
		}

		//输出格式去掉all
		$this->_aInput['output']['format'] = preg_replace('/_all$/','',$this->_aInput['output']['format']);
		//var_dump($this->_aInput['output']['format']);exit;
	}

	/**
	*	判断是否为全部导出
	*
	*/
	public function isExportAll(){
		if(isset($this->_aInput['output']['format'])){
			$formatType = $this->_aInput['output']['format'];
			if(in_array($formatType,$this->_aTypeAll)){
				return true;
			}
		}
		return false;
	}

	private function _getData(){
		//total和flash输出清空
		if(!empty($this->_aInput['input']['total'])){
			$this->_aInput['input']['total'] = array();
		}
		if(!empty($this->_aInput['input']['flash'])){
			$this->_aInput['input']['flash'] = array();
		}

		//检查输出格式 不能为全部导出 否则进入死循环
		if($this->isExportAll()){
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','can not export all here,please chkAndParseInput first');
		}
		\YcheukfReport\Lib\ALYS\ALYSFunction::clear();
		$oReport = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass('report.start',$this->_aInput);
		$oRe = $oReport->go();
		return array('data'=>$oRe['detail.output'],'num'=>$oRe['detail.num']);
	}

	//处理数据
	private function _processData($data,$exportType='download',$property=array()){
		switch($exportType){
			case 'file':
				error_log($data,3,$property['fileName']);
			break;
			case 'download':
			default:
				ob_start();
				ob_end_flush();
				echo $data;
				ob_flush();
				flush();
			break;
		}
	}

	private function _getFlsTotal(){
		$aInput = $this->_aInput;
		if(empty($aInput['input']['total'])&&empty($aInput['input']['flash'])){
			return false;
		}
		$aInput['input']['detail'] = array();
		\YcheukfReport\Lib\ALYS\ALYSFunction::clear();
		$oReport = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass('report.start',$aInput);
		$oRe = $oReport->go();
		return $oRe['output'];
	}

	private function _conv($content){
		$encode = mb_detect_encoding($content, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
//		if('GBK'!==$encode){
//			$content = iconv($encode, "GBK", $content);
//		}
		return $content;
	}
}