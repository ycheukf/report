<?php
namespace YcheukfReport\Lib\ALYS;
/**
* �Զ���һ���쳣������
*/
class ALYSException extends \Exception
{		
    // �ض��幹����ʹ message ��Ϊ���뱻ָ��������
    public function __construct($message='',$param='', $code = 0) {
		\YcheukfCommon\Lib\Functions::debug($message.' '.$param, "[inline]---[exception]---".__CLASS__);
        parent::__construct($message.' '.$param, $code);
    }
    // �Զ����ַ����������ʽ */
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