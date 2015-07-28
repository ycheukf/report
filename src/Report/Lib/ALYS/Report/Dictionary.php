<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Dictionary extends \YcheukfReport\Lib\ALYS\Report{
	
	
	
	public function __construct(){

	}

    public function __call($name, $arguments) {
		throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXT_DICTIONARY_CALL_NOT_EXISTS_FUNC'.'\n function:'.$name.'\n ');
    }
	
	/**
	* ¼æÈÝidigger
	*/
	public function _($name){
		\YcheukfReport\Lib\ALYS\ALYSLang::_($name);
	}
}
?>