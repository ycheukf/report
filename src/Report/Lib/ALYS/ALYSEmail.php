<?php
namespace YcheukfReport\Lib\ALYS;
use PEAR\Mail;
use PEAR\Mail\mime;
/**
* 邮件类
* 用于发送邮件
*/
class ALYSEmail {
	var $headers = array();
	var $config = array();
	var $mail_object;
	var $mime;
	var $hdrs = array();
	function __construct(){		
//		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();			
//		$this->config['mail'] =$_ALYSconfig['email'];
//		$this->mail_object = &\Mail::factory('smtp', $this->config['mail']);//返回一个smtp类
	}
	/*
	* 邮件发送
	* $to 发送人邮箱
	* $from 发信人地址
	* $subject 邮件标题
	* $file 附件内容
	*/
	function send($to,$subject,$body,$file=''){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
        \YcheukfCommon\Lib\Functions::sendEmailByMQ($_ALYSconfig['smHandle'], $to, $subject, $body);
        return true;

        //旧的逻辑
		$this->hdrs['From'] = $this->config['mail']['username']; //发信地址 
		$this->hdrs['To'] =strtr($to,';',','); ; //收信地址 
		$this->hdrs['Subject'] = $subject; //邮件标题
		$this->mime = new Mail_mime();
		$this->mime->_build_params['html_charset'] = "utf-8";//设置编码格式
		$this->mime->_build_params['head_charset'] = "utf-8";//设置编码格式
		$this->mime->setHTMLBody($body); //设置邮件正文
		if($file <> ""){			
			$this->mime->addAttachment($file, 'application/octet-stream');//设置附件纯文本内容text/html或设置成		application/octet-stream 附件下载
		}
		$new_body = $this->mime->get(); 
		$headers = $this->mime->headers($this->hdrs);
		$ret = $this->mail_object->send($this->hdrs['To'],$headers,$new_body);//发送邮件
		//var_dump($ret);
		if(PEAR::isError($ret)){ //检测错误
			$s = $ret->getMessage();
			throw new \YcheukfReport\Lib\ALYS\ALYSException('ALYSEMAIL_ERROR', $s);
			return false;
		}else{			
			return true;
		}
	}
}
?>