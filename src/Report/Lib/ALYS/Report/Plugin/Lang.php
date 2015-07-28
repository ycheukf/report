<?php
/**
* 语言plugin
*
* 负责多语言的实现
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Lang extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}


	/**
	 *	负责报表内部的的语言转换

	 @param string s 用户传进去的键值
	 @return string 翻译后的语言, 找不到为键值
	 @description
	 */
	public function ALYSbefore_translation($s){
		$aLang = $this->_getLangAry();
		return isset($aLang[$s]) ? $aLang[$s] : $s;
	}

	/**
	* 语言数组, 键值会转换为大写后再输出
	*/
	public function _getLangAry(){
		$aLang = array(
			'SELECT' => '',
			'DBCONFIGOP' => '操作',
			'CUSTOMERID' => '客户ID',
			'SERVERNAME' => '主机名',
			'MASTERHOST' => '主机IP',
			'RE_DIMEN_EDIT' => '编辑属性',
			'RE_DIMEN_UPGRADE' => 'DB升级',
			'RE_DIMEN_DOWNGRADE' => 'DB降级',
			'RE_DIMEN_CONFIG' => '调整配置',
			'NEWESTVERSION ' => '最新版本',
			'CURRENTVERSION ' => '当前版本',

		);

		$smHandle = \YcheukfReport\Lib\ALYS\ALYSConfig::get('smHandle');
		$aRe = array();
		foreach($aLang as $k => $v){
			$aRe[strtoupper(trim($k))] = $smHandle->get('translator')->translate($v, 'reportengine');
		}
		return $aRe;
	}
}
?>