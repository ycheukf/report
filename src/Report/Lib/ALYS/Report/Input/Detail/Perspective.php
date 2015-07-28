<?php
namespace YcheukfReport\Lib\ALYS\Report\Input\Detail;
class Perspective extends \YcheukfReport\Lib\ALYS\Report\Input\Detail{

	public function __construct(){
		parent::__construct();

	}

	public function preStart(){
		$aInput= $this->aInput['input'][$this->_type]['table'];
		//检查 横向维度 并初始化:将xdimen加入到dimen
		$i = 1;
		foreach($aInput as $table=> $aTable){
			if($i==1){
				if(empty($aTable['xdimen'])||count($aTable['xdimen'])!=1){
					throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_KEY_WRONG','need xdimen,and just one xdimen');
				}
				$aInput[$table]['dimen'] = array_merge($aTable['dimen'],$aTable['xdimen']);

				foreach($aTable['xdimen'] as $v){
					$aInput[$table]['xdimen_key'][] = $v['key'];
				}
				foreach($aTable['dimen'] as $v){
					if(!(isset($v['group'])&&false==$v['group']))
					{
						//去重
						if(empty($aInput[$table]['ydimen_key'])||!in_array($v['key'],$aInput[$table]['ydimen_key']))
						{
							$aInput[$table]['ydimen_key'][] = $v['key'];
						}
						$aInput[$table]['ydimen_key_select'][$v['key']] = $v['selected']?$v['selected']:$v['key'];
					}
				}
			}
			$i++;
			//$aInput[$table]['xdimen'][] = $aTable['xdimen'][]
		}
		$this->aInput['input'][$this->_type]['table'] = $aInput;

		\YcheukfReport\Lib\ALYS\Report\Start::setInput($this->aInput);

	}



}
?>