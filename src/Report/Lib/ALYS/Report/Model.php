<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Model extends \YcheukfReport\Lib\ALYS\Report{
	public $oDbp;//db oparator;
	public static $oInstant;//db oparator;
	public function __construct(){
		if(is_null(self::$oInstant)){
			$this->setOdbp();
		}
		$this->oDbp = self::$oInstant;
	}
	
	/**
	* set db object
	*/
		
	public function setOdbp(){
		$sDboperator = \YcheukfReport\Lib\ALYS\ALYSConfig::get('dboperator');
		self::$oInstant = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("dboperate.".$sDboperator);
	}
	
	
	
	function getAlldata($aConf){
		$aData=$this->oDbp->getAll($aConf);
		$this->oDbp->sql_clear();
		//print_r($aData);
		return $aData;
		
	}
	
	
	
	
}	

?>