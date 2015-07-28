<?php

namespace YcheukfReport\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DemoController extends AbstractActionController
{

    public function indexAction()
    {
		$aEntity = $this->getServiceLocator()->get('ZmcBase\Mapper\AlbumMapper')->findById(2);
		$aConfig = $this->getServiceLocator()->get('config');
		var_dump($aConfig['cache_dir']);
//cache_dir
		$aInput = array(
			'filters' => array( //ѡ��. Ĭ��Ϊ��, ��������. 
				array(
					'key' => 'domainId', //������
					'op' => 'in', //������=,in,like
					'value' => '(651, 594)'//���˵�ֵ
				),
			),
			'input'=>array(
				'detail'=>array(
					'type' =>'table',
					'orderby' =>'id asc',
					'table' => array(
						'album' => array(
							'dimen' => array(
								'id',
								'artist',
								'title',
							),
						),
					),
				),
			),
			'output' => array(
				'format' => 'html',
			),

		);
		$oReport = $this->getServiceLocator()->get('YcheukfReportService')->loadClass('report.start', $aInput);
		$aRe = $oReport->go();
		echo ($aRe['output']);
        return new ViewModel();
    }

	function exportpluginAction(){
		$aExtDir = array('ALYS.Report.Dictionary', 'ALYS.Report.Plugin');
//		var_dump(LIB_PATH_ALYS);
//		var_dump(EXT_PATH_ALYS);
		$LIB_PATH_ALYS = "D:\htdocs\work\apps\code\l_project_web\module\ycheukf\Report\src\Report\Lib";
		$EXT_PATH_ALYS = "D:\htdocs\work\apps\code\l_project_web\module\Application\src\Application\YcheukfReportExt";
		foreach($aExtDir as $sTmp){
			$path = str_replace(".", '/', $sTmp);
			$sDirPath = $LIB_PATH_ALYS.'/'.$path;
			$sExtDirPath = $EXT_PATH_ALYS.'/'.str_replace("ALYS/", "",(string)$path);	//��չĿ¼, ����Ҫ��·����Ĵ˴�
		//	var_export($sDirPath);
			$it = new \DirectoryIterator($sDirPath);
			foreach($it as $file) {
				if (!$it->isDot() && $file!='.svn' && $file!='.git') {
					$this->recursiveMkdir($sExtDirPath, '0700');

					copy($sDirPath.'/'.$file, $sExtDirPath.'/'.$file);
					$sContent = file_get_contents($sExtDirPath.'/'.$file);
//					$sContent = preg_replace("/require_once\(\"".str_replace(".", '\/', $sTmp).".php\"\);/s", ' ', $sContent);
					$sClassName = str_replace(".php", "",(string)$file);
					preg_match_all('/namespace (.*);/', $sContent, $aMatch);
//					var_dump((string)$file);
//					var_dump($aMatch[1][0]);
					$sContent = str_replace("namespace YcheukfReport\Lib\ALYS", 'namespace YcheukfReportExt', $sContent);
					$sContent = preg_replace("/class ([^\s]+?) extends ([^}]+?)\{/s", 'class \1 extends \2\\'.$sClassName.'{', $sContent);
		//			echo $sContent;
					file_put_contents($sExtDirPath.'/'.$file, $sContent);
				}
			}
		}

		echo "\n\n export file to ext   ... DONE\n\n";
		exit;

	}

	/**
	* �ݹ鴴��Ŀ¼����
	*
	* @param $path ·�������� "aa/bb/cc/dd/ee"
	* @param $mode Ȩ��ֵ��php��Ҫ��0��ͷ������0777,0755
	*/
	  function recursiveMkdir($path,$mode)
	   {
		   if (!file_exists($path))
		   {
			   $this->recursiveMkdir(dirname($path), $mode);
			   mkdir($path, $mode);
		   }
	   }
	 


}

