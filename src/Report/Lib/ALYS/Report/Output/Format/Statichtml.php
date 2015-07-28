<?php
namespace YcheukfReport\Lib\ALYS\Report\Output\Format;
class Statichtml extends \YcheukfReport\Lib\ALYS\Report\Output\Format{

	public function __construct(){
		parent::__construct();
		
	}
	public function go(){
		$aOutput = \YcheukfReport\Lib\ALYS\Report\Start::getOutput();
		$sHtml = "";
		$oPluginStatic = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Statichtml");
		if(isset($aOutput['flash.output'])){
			$sHtml .= $oPluginStatic->ALYSfmtOutputFlash($aOutput['flash.output']);
		}
		if(isset($aOutput['total.output'])){
			$aOutput['total.output'] = parent::_forma_html_total($aOutput['total.output']);
			$sHtml .= $oPluginStatic->ALYSfmtOutputTotal($aOutput['total.output']);
		}
		if(isset($aOutput['detail.output'])){
			$aOutput['detail.output'] = parent::_format_html_list($aOutput['detail.output']);
			$sHtml .= $oPluginStatic->ALYSfmtOutputList($aOutput['detail.output']);
		}
		
		$sHtml = self::_fmtHtml($sHtml);
		
		$cache	= \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("ALYSCache");	
		$cache->setHandle('report_static_html');
		$keyName = uniqid();
		$cache->save($keyName, $sHtml);
		$aOutput['output'] = $keyName;
		\YcheukfReport\Lib\ALYS\Report\Start::setOutput($aOutput);
		return($keyName);
	}
	
	
	private static function _fmtHtml($sHtml){
		$s = <<<OUTPUT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>[replace_title]</title>
[replace_css_file]
[replace_script_file]
<script type="text/javascript">
			[replace_script]
</script>
<style>
[replace_style]
</style>
<body>
	{$sHtml}
</body>
</html>

OUTPUT;

		$oPlugin = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Statichtml");
		return $oPlugin->ALYSbefore_output($s);
	}
}
?>