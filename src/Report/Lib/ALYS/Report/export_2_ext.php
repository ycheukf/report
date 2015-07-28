<?php
require_once dirname(__FILE__).'/../../go.php';
$aExtDir = array('ALYS.Report.Dictionary', 'ALYS.Report.Plugin');
foreach($aExtDir as $sTmp){
	$path = str_replace(".", '/', $sTmp);
	$sDirPath = LIB_PATH_ALYS.'/'.$path;
	$sExtDirPath = EXT_PATH_ALYS.'/lib/'.$path;	//扩展目录, 若需要改路径请改此处
//	var_export($sDirPath);
	$it = new DirectoryIterator($sDirPath);
	foreach($it as $file) {
		if (!$it->isDot() && $file!='.svn') {
//			echo $file . "<br>";
			RecursiveMkdir($sExtDirPath, '0700');

			copy($sDirPath.'/'.$file, $sExtDirPath.'/'.$file);
			$sContent = file_get_contents($sExtDirPath.'/'.$file);
			$sContent = preg_replace("/require_once\(\"".str_replace(".", '\/', $sTmp).".php\"\);/s", ' ', $sContent);
			$sContent = preg_replace("/class ([^\s]+?) extends ([^}]+?)\{/s", 'class Ext_\1 extends \1{', $sContent);
//			echo $sContent;
			file_put_contents($sExtDirPath.'/'.$file, $sContent);
		}
	}
}

echo "\n\n export file to ext   ... DONE\n\n";


/**
* 递归创建目录函数
*
* @param $path 路径，比如 "aa/bb/cc/dd/ee"
* @param $mode 权限值，php中要用0开头，比如0777,0755
*/
  function recursiveMkdir($path,$mode)
   {
       if (!file_exists($path))
       {
           RecursiveMkdir(dirname($path), $mode);
           mkdir($path, $mode);
       }
   }
 
 







