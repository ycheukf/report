<?php
namespace YcheukfReport\Lib\ALYS\Report;

class Static extends \YcheukfReport\Lib\ALYS\Report{

	public function __construct(){
		
	}
	
	public function go($cacheKey){
		$cache = \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass('ALYSCache');	
		$cache->setHandle('report_static_html');
		$s = $cache->get($cacheKey);
		return $s;
	}
	//自定义传入HTML 生成静态文件 允许多个flash,total,list传入
	public function goSave($aHtml){
		$sHtml = '';
		$oPluginStatic = \YcheukfReport\Lib\ALYS\ALYSFunction::loadPlugin("Statichtml");
		if(!empty($aHtml['flash'])){
			if(is_array($aHtml['flash'])){
				foreach($aHtml['flash'] as $fhtml){
					
					$sHtml .= $oPluginStatic->ALYSfmtOutputFlash($fhtml);
				}
			}else{
				$sHtml .= $oPluginStatic->ALYSfmtOutputFlash($aHtml['flash']);
			}
		}
		
		if(!empty($aHtml['total'])){
			if(is_array($aHtml['total'])){
				foreach($aHtml['total'] as $thtml){
					$thtml=\YcheukfReport\Lib\ALYS\Report\Output\Format::_forma_html_total($thtml);
					$sHtml .= $oPluginStatic->ALYSfmtOutputTotal($thtml);
				}
			}else{
				$aHtml['total']=\YcheukfReport\Lib\ALYS\Report\Output\Format::_forma_html_total($aHtml['total']);
				$sHtml .= $oPluginStatic->ALYSfmtOutputTotal($aHtml['total']);
			}
		}
		
		if(!empty($aHtml['detail'])){
			if(is_array($aHtml['detail'])){
				foreach($aHtml['detail'] as $lhtml){
					$lhtml = \YcheukfReport\Lib\ALYS\Report\Output\Format::_format_html_list($lhtml);
					$sHtml .= $oPluginStatic->ALYSfmtOutputList($lhtml);
				}
			}else{
				$aHtml['detail'] = \YcheukfReport\Lib\ALYS\Report\Output\Format::_format_html_list($aHtml['detail']);
				$sHtml .= $oPluginStatic->ALYSfmtOutputList($aHtml['detail']);
			}
		}
		
		$sHtml = self::_fmtHtml($sHtml);
		
		$cache	= \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass("ALYSCache");	
		$cache->setHandle('report_static_html');
		$keyName = uniqid();
		$cache->save($keyName, $sHtml);
		return $keyName;
	}

	function _fmtHtml($sHtml){
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