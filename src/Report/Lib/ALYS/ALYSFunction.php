<?php
namespace YcheukfReport\Lib\ALYS;
/**
* 
* 通用函数
*/

class ALYSFunction{
	protected static $instancesDb = array();
	protected static $instancesClass = array();
	protected static $instancesPlugin = array();
	protected static $instancesDictionary = array();
	protected static $aClassMapper = array();
	
	/**
	* 重置ALYS的所有对象,配置, 为了兼容多个实例共存
	*/
	public static function clear(){
		self::$instancesDb = array();
		self::$instancesClass = array();
		self::$instancesPlugin = array();
		self::$instancesDictionary = array();
	}

	public static function loadClass($className='', $p=null){
		if (empty(self::$instancesClass[$className]))
		{
			\YcheukfReport\Lib\ALYS\ALYSFunction::debug($className, 'a', 'load class');
			$o = self::getCoreClassNamePath($className, $p);
			if(!$o)
				throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXT_CLASS_NOT_EXISTS'."\n path=".$className);
			self::$instancesClass[$className] = $o;
		}
//			var_export(self::$instancesClass);
		return self::$instancesClass[$className];
	}
	
	public static function setClassMapper($sClassName, $sClassPath){
		self::$aClassMapper[$sClassName] = $sClassPath;
	}
	/** debug 函数
	@param mix data 调试的数据
	@param string method 写文件方法,传'w'则覆盖,传'a'则续写
	@param string memo 摘要信息
	@param array aCustomParam 自定义参数 xmp=>是否使用xmp来渲染
	@author feng
	@return bool
	*/
	public static function debug($data,$method="a", $memo='None', $aCustomParam=array('xmp'=>1))
	{
		$sm = \YcheukfReport\Lib\ALYS\ALYSConfig::get('smHandle');
		$aConfig = $sm->get('config');
		if(isset($aConfig['YcheukfReport']['debug']) && $aConfig['YcheukfReport']['debug'] ==1)
			return \YcheukfCommon\Lib\Functions::debug($data, "[inline]---[report]---".$memo, $aCustomParam);
		return true;
	}

	public static function ucfirst($s){
		return ucfirst(strtolower($s));	
	}										   

	/**
	* 加载plugin类
	*/
	public static function loadPlugin($className){

		if (empty(self::$instancesPlugin[$className]))
		{
			$oPlugin = self::getExtClassNamePath("Report.Plugin.".$className);
			if($oPlugin){
				self::debug($className, 'a', 'load ext plugin');
				self::$instancesPlugin[$className] = $oPlugin;
			}else if($oPlugin = self::getCoreClassNamePath("Report.Plugin.".$className)){
				self::debug($className, 'a', 'load core plugin');
			}else{
				$sMsg = 'Ext_Plugin_NOT_EXISTS'."\n path=".$className;
				throw new \YcheukfReport\Lib\ALYS\ALYSException($sMsg);
			}
			self::$instancesPlugin[$className] = $oPlugin;
		}
		return self::$instancesPlugin[$className];


	}

	
	/**
	* 处理特殊字符串
	*/
	public static function _htmlspecialchars($str){
		$afusion = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Flash");
		return $afusion->ALYShtmlspecialchars($str);
	}
	/**
	* 加载dictionary类
	*/
	public static function loadDictionary($className){
		if (empty(self::$instancesDictionary[$className]))
		{
			if (isset(self::$aClassMapper[$className])) {
				self::$instancesDictionary[$className] = new self::$aClassMapper[$className]();
			}else{
				$oDict = self::getExtClassNamePath("Report.Dictionary.".$className);
				if($oDict){
					self::debug($className, 'a', 'load ext dictionary');
					self::$instancesDictionary[$className] = $oDict;
				}else if($oDict = self::getCoreClassNamePath("Report.Dictionary.".$className)){
					self::debug($className, 'a', 'load core dictionary');
					self::$instancesDictionary[$className] = $oDict;
				}else{
					throw new \YcheukfReport\Lib\ALYS\ALYSException('Ext_Dictionary_NOT_EXISTS'."\n path=".$className);
				}
			}
		}
		return self::$instancesDictionary[$className];
	}
    public static function getMicroTime($time='')
    {   
		$time = empty($time) ? microtime() : $time;
		list($em, $es) = explode(' ', $time);
        return (float)$em + (float)$es;
    }
	/**
	*	@按生命周期删除文件
	**/
	public static function removeLifeFile($pathName,$times,$exts = false){
		$result = false;
		if(!is_dir( $pathName ) || !is_readable( $pathName )){
			return false;
			
		}
		$handle = opendir( $pathName );
		while ( false !== ($filename = readdir( $handle ) ) ) {
			$fullfile = $pathName."/".$filename;
			if( ! is_file ( $fullfile ) ) continue;			
			if($exts === false ){
				if(self::_unlink($fullfile,$times))
					$result = true;
			}else{			
				$info = pathinfo($fullfile);
				if(in_array(strtolower($info["extension"]),$exts)){					
					if(self::_unlink($fullfile,$times))
						$result = true;
				}
			}		
		}
		closedir( $handle );
		return $result;
	}


	/**
	*	加载DB句柄
	**/
	public static function loadDb(){
		$sDbHandle = 'dbHandle';
		$oDbHandle = \YcheukfReport\Lib\ALYS\ALYSConfig::get($sDbHandle);

		return $oDbHandle;
	}

	public static function _getClassPath($className){
		$a = explode(".", $className);
		foreach($a as $k=>$s){
            $bFlag = preg_match("/alys/i", $s) ? true : false;
            if($bFlag){
                 $s = str_replace("alys", '', strtolower($s));
                 $s = "ALYS". \YcheukfReport\Lib\ALYS\ALYSFunction::ucfirst($s);
            }else
    			$a[$k] = \YcheukfReport\Lib\ALYS\ALYSFunction::ucfirst($s);
		}
		return $a;
	}
	/**
	* 获取核心类的路径与名称
	*/
	public static function getCoreClassNamePath($className, $p=null){
		$aPath = self::_getClassPath($className);
		$sClassName = "\\".__NAMESPACE__."\\".join("\\", $aPath);
		if(!class_exists($sClassName))
			return false;
		if(is_null($p))
			$o = new $sClassName();
		else
			$o =new $sClassName($p);
		return $o;
	}
	/**
	* 获取扩展类的路径与名称
	*/
	public static function getExtClassNamePath($className, $p=null){
		$aPath = self::_getClassPath($className);
//		$sClassName = "\\".str_replace("Lib\\", "Ext\\Lib\\", __NAMESPACE__)."\\".join("\\", $aPath);
		$aSplit = explode("\\", __NAMESPACE__);
		$sClassName = "\\".$aSplit[0]."Ext\\".join("\\", $aPath);
		if(!class_exists($sClassName))
			return false;
		if(is_null($p))
			$o = new $sClassName();
		else
			$o =new $sClassName($p);
		return $o;

	}

	static function iconv2SpecialCharset($sMessage, $sOutCharset){

	    $sEncode = mb_detect_encoding($sMessage , array('UTF-8','GB2312', 'GBK','LATIN1','BIG5'));
	    if ($sEncode != $sOutCharset) {
	        $sMessage = iconv($sEncode, $sOutCharset, $sMessage);
	    }
	    return $sMessage;
	}

	static function iconv2Utf8($sMessage){

	    $sEncode = mb_detect_encoding($sMessage , array('UTF-8','GB2312', 'GBK','LATIN1','BIG5'));
	    if ($sEncode != 'UTF-8') {
	        $sMessage = iconv($sEncode, "UTF-8", $sMessage);
	    }
	    return $sMessage;
	}
	/**
	*	@私有方法——删除文件
	**/
	function _unlink($file,$times){
		$filelife=$times;
		if((filectime($file)+$filelife)-time()<0){
			@unlink($file);
			return true;
		}
		return false;
	}
}