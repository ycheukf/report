<?php 
namespace YcheukfReport\Lib\ALYS;
/**
* 报表类
* 用于生成PDF的报表
*/
//require_once(LIB_PATH_ALYS.'/tcpdf/config/lang/eng.php');
//require_once(LIB_PATH_ALYS.'/tcpdf/tcpdf.php');
class ALYSPdf {

	public $pdf = null;
	public $_name ;
	public $_type = 'I';
	function __construct(){
		$this->_name='_ALYS'.uniqid().".pdf";
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$_alys=$_ALYSconfig['pdf'];
		if(is_null($this->pdf))
			$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);		
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor($_alys['author']);
		$this->pdf->SetTitle($_alys['title']);
		$this->pdf->SetSubject($_alys['subject']);
		$this->pdf->SetKeywords($_alys['keyword']);

		// set header and footer fonts
		$this->pdf->setHeaderFont(Array('stsongstdlight', '', 9));
		$this->pdf->setFooterFont(Array('stsongstdlight', '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$this->pdf->setLanguageArray($l);

		// set default font subsetting mode
		$this->pdf->SetFont('stsongstdlight','B', 10);
	}
	function addPage(){
		$this->pdf->AddPage();
	}
	function Ln($n=1){
		$this->pdf->Ln($n);
	}
	function lastPage(){
		$this->pdf->endPage(true);
	}
	function writeHTML($shtml){		
		$this->pdf->writeHTML($shtml, true, false, true, false);	
	}
	function writeHTMLCell($shtml){		
		$this->pdf->writeHTMLCell(250, 0, 15, 25, $shtml, 0, 0, 0, true, 'C');
	}

	function image($src,$l,$t,$w,$h){	
		$this->pdf->Image($src,$l,$t,$w);
	}
	function read(){		
		$this->_type=strtoupper($this->_type);
		if($this->_type === 'F'){			
			$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
			$this->_name=$_ALYSconfig['pdf']['path'].$this->_name;
			$this->pdf->Output($this->_name,$this->_type);
		}else{
			$this->pdf->Output($this->_name,$this->_type);
			/* 默认是I：在浏览器中打开，D：下载，F：在服务器生成pdf ，S：只返回pdf的字符串*/
		}
	}
	function setInfo($name = '',$type,$top,$headFoot,$pageFooter){
		// Set some content to print
		if(!is_null($name))
			$this->_name=$name;
		$this->_type=$type;
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$this->pdf->SetHeaderData($_ALYSconfig['pdf']['img'],40,$top,$headFoot);
		$this->pdf->setLanguageArray(array('w_page'=> $pageFooter));
		return $this->_name;
	}
	/**
	*  @递归数组
	*  @return Array
	*  @author mFeng
	*/
	function aRecur($_aRe){
		static $_akv = array();
		if(is_array($_aRe)){
			foreach($_aRe as $k => $v){
				$_akv[] = $k.".".$v;
				self::aRecur($v);
			}
		}
		return $_akv;
	}
	/**
	*  @正则替换对应样式
	*  @return string
	*  @author mFeng
	*/
	function replaceCss($aCss,$sS){
		$sCss="";
		if(!is_array($aCss))return false;
//		foreach($aCss as $v){
//			$a = explode(".",$v);
//			if($a[0]==="table"){
//				$sCss=preg_replace("/<table>/i","<table ".$a[1].">",$sS);
//			}elseif($a[1]<>"Array" && $a[1]<>"" ){
//				$sCss=preg_replace("/class=[\"|\']".$a[0]."+[\"|\']/i","style=\"".$a[1]."\"",$sCss);
//			}
//		}
		return $sCss;
	}
	/**
	*  @图片设置
	*  @return string
	*  @author mFeng
	*/
	function setImg($flash){
		$img='<img src="'.$flash.'" alt="" height="250px" width="850px;"/>';		
		return $img;
	}
}