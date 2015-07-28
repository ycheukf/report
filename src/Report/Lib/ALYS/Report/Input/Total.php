<?php
namespace YcheukfReport\Lib\ALYS\Report\Input;
class Total extends \YcheukfReport\Lib\ALYS\Report\Input{

	public function __construct(){
		
		parent::__construct();
	}
	/**
	*Èë¿Ú
	*/
	public function fmtInput(){
		$type='total';
		$this->_initaInput($type);
		$this->initInput($type);
	}
	

	
	

}
?>