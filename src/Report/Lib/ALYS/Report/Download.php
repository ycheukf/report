<?php
/**
	负责报表的下载类
 */
namespace YcheukfReport\Lib\ALYS\Report;

class Download extends \YcheukfReport\Lib\ALYS\Report{	
	public function __construct(){}
	
	public function pdf($filePath,$exportName){
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$path = $_ALYSconfig['pdf']['path'];
		$path = $path.$filePath;	
		self::_download($path,$exportName);
	}
	/**
	*	下载文件类
	*	@$file 文件路径
	**/
	function _download($file,$exportName){
		$defaultName = $exportName ;
		//First, see if the file exists
		if (!is_file($file)) { die("<b>404 File not found!</b>"); }
		//Gather relevent info about file
		$len = filesize($file);
		$filename = basename($file);
		$file_extension = strtolower(substr(strrchr($filename,"."),1));

		//This will set the Content-Type to the appropriate setting for the file
		switch( $file_extension ) {
		  case "pdf": $ctype="application/pdf"; break;
		  case "exe": $ctype="application/octet-stream"; break;
		  case "zip": $ctype="application/zip"; break;
		  case "doc": $ctype="application/msword"; break;
		  case "xls": $ctype="application/vnd.ms-excel"; break;
		  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		  case "gif": $ctype="image/gif"; break;
		  case "png": $ctype="image/png"; break;
		  case "jpeg":
		  case "jpg": $ctype="image/jpg"; break;
		  case "mp3": $ctype="audio/mpeg"; break;
		  case "wav": $ctype="audio/x-wav"; break;
		  case "mpeg":
		  case "mpg":
		  case "mpe": $ctype="video/mpeg"; break;
		  case "mov": $ctype="video/quicktime"; break;
		  case "avi": $ctype="video/x-msvideo"; break;

		  //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
		  case "php":
		  case "htm":
		  case "html":
		  case "txt": //die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;
		  default: $ctype="application/force-download";
		}

		//Begin writing headers;
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer");
		
		//Use the switch-generated Content-Type
		header("Content-Type:".$ctype);

		//Force the download
		$filename=$defaultName.".".$file_extension;
		
		//文件名转成GBK输出 测试ff12.0 ie8.0 chrome无问题
		$encode = mb_detect_encoding($filename, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
		if('GBK'!==$encode){
			$filename = iconv($encode, "GBK", $filename);
		}
		$header='Content-Disposition: attachment; filename="'.$filename;
		header($header);
		header("Content-Transfer-Encoding:binary");
		header("Content-Length: ".$len);
		@readfile($file);
		exit;
	}

}
?>