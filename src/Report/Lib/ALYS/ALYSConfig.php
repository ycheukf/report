<?php
namespace YcheukfReport\Lib\ALYS;
/**
	@version V1.0 Nov 2011   (c) 2011-2012 (allyes.com). All rights reserved.
	报表通用类
 */

class ALYSConfig{
	
	private static $config = array();

	public function __construct(){
			
	}

	public static function set($key,$value){
		self::$config[$key]=$value;
	}
	
	public static function setFusion($key,$value){
		self::$config['fusion'][$key]=$value;
	}

	public static function setAll($a){
		self::$config = $a;
	}
	public static function get($key=null) {
		if(is_null($key)){
			return self::$config;
		}else{
			if(isset(self::$config[$key]))
				return self::$config[$key];
			else
				throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXPT_CONFIG_NOT_SET', $key);
		}
	}

}
?>