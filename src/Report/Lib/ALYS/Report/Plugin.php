<?php
namespace YcheukfReport\Lib\ALYS\Report;
class Plugin extends \YcheukfReport\Lib\ALYS\Report{
	var $aInput;
	public function __construct(){
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();
	}

    public function __call($name, $arguments) {
		throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEXT_PLUGIN_CALL_NOT_EXISTS_FUNC'.'\n function:'.$name.'\n ');
    }

}
?>