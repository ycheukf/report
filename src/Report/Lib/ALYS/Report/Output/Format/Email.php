<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Email extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	public function go(){
		$o = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("report.output.format.statichtml");
//		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$sStaticKey = $o->go();
		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("email");
		$aInfo = $oPlugin->ALYSbefore_email($sStaticKey);

		$m=\YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("ALYSEmail");
		$m->send($aInfo['toEmail'],$aInfo['subject'],$aInfo['body']);
//		return $flag?1:0;
	}
}
?>