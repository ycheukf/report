<?php
/**
* Email 输出的plugin
* 
* 负责EMAIL输出的扩展
*
* @author   ycheukf@gmail.com
* @package  Plugin
* @access   public
*/
namespace YcheukfReport\Lib\ALYS\Report\Plugin;
class Email extends \YcheukfReport\Lib\ALYS\Report\Plugin{
	public function __construct(){
		parent::__construct();
	}


	/**
	 *	负责Email发送

	 @param string staticKey 静态html链接的key
	 @return array 处理过后的email数组
	 @description 处理
	 */
	public function ALYSbefore_email($staticKey){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$aInput = \YcheukfReport\Lib\ALYS\Report\Start::getInput();

		//配置静态页面时间
		$time = $_ALYSconfig['cache']['report_static_html']['lifeTime'];
		$nDate = intval($time/(3600*24));

		//配置静态页面链接
		$url = $_ALYSconfig['rooturl']."demo/advance_get_statichtml.php";



		//配置email信息
		$aEmail = array();
		$mailBody = isset($aInput['custom']['email']['mailBody']) ? $aInput['custom']['email']['mailBody'] : "hello world";
		$aEmail['subject'] = 'report engine';
		$aEmail['fromEmail'] = 'report.engine@allyes.com';
		$aEmail['toEmail'] = isset($aInput['custom']['email']['toEmail']) ? join(',', $aInput['custom']['email']['toEmail']) : "'ycheukf@gmail.com,ruzhuo_feng@allyes.com'";//逗号分割
		$aEmail['body'] = <<<OUTPUT
			<p>您好: 
			{$mailBody}
			<p>	请点击链接查看报表
			<p>	{$url}?id={$staticKey}
			<p>	本条链接在{$nDate}天内有效
OUTPUT;
			//var_export($aEmail);
		return $aEmail;
	}
}
?>