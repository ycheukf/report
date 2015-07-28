<?php
namespace YcheukfReport\Lib\ALYS;
/**
* 自定义一个异常处理类
*/
class ALYSException extends \Exception
{		
    // 重定义构造器使 message 变为必须被指定的属性
    public function __construct($message='',$param='', $code = 0) {
		\YcheukfCommon\Lib\Functions::debug($message.' '.$param, "[inline]---[exception]---".__CLASS__);
        parent::__construct($message.' '.$param, $code);
    }
    // 自定义字符串输出的样式 */
    public function __toString() {
//        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    public function msg() {
		$errArr=array();
		$errCode=$this->getCode();//($this->getCode())   ? '' : $this->getCode();
		$errMsg =$this->getMessage();//empty($this->getMessage())? '' : $this->getMessage();
		$errLine=$this->getLine();
		$errFile=$this->getFile();
		$traceStr=$this->getTraceAsString();  
		$errArr['code']=$errCode;
		$errArr['line']=$errLine;
		$errArr['file']=$errFile;
		$errArr['msg'] =$errMsg;
		$errArr['trace'] =$traceStr;		
		return $errArr;
    }
}

?>