<?php
namespace YcheukfReport;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use YcheukfReport\Lib\ALYS\Test;

use YcheukfReport\Lib\ALYS\ALYSFunction;

class Service implements ServiceManagerAwareInterface
{
    public function loadClass($sClass, $aInput=array()){
//		var_dump(class_exists('Zend\ServiceManager\ServiceManager2'));
//		$aInput = $this->fmtReportParams($aInput);
		return \YcheukfReport\Lib\ALYS\ALYSFunction::loadClass($sClass, $aInput);
	}
	
    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function fmtReportParams($aReturn, $aReportParams=array()){

        /*记住历史选择**/
        if(isset($aReportParams['initflag'])){
            $sListConfigKey = "reportservice_listconfig_".md5($_SERVER['REQUEST_URI']);
            $sTmp = $this->getServiceManager()->get('Zend\Session\SessionManager')->getStorage()->$sListConfigKey;
            if( 1 == $aReportParams['initflag'] && !is_null($sTmp)){//默认请求, 则从session中读取
                $aReportParams['params'] = $sTmp;
            }
            $this->getServiceManager()->get('Zend\Session\SessionManager')->getStorage()->$sListConfigKey = $aReportParams['params'];
        }

		if(isset($aReportParams['params']) && isset($aReturn['input']['detail']) && count($aReportParams)){
			$aTableParams = json_decode($aReportParams['params'], 1);
//var_dump($aTableParams);
			$aReturn['input']['detail']['page']['items_per_page'] = isset($aTableParams['items_per_page']) ? $aTableParams['items_per_page'] : 10;
			$aReturn['input']['detail']['page']['current_page'] = isset($aTableParams['current_page']) ? $aTableParams['current_page'] : 0;
			$aTableParams['sort_type'] = isset($aTableParams['sort_type']) ? $aTableParams['sort_type'] : 'asc';
			$aTableParams['sort_by'] = isset($aTableParams['sort_by']) ? $aTableParams['sort_by'] : null;
			if(isset($aTableParams['sort_by']) && !empty($aTableParams['sort_by']) && !is_null($aTableParams['sort_by']))
				$aReturn['input']['detail']['orderby'] = $aTableParams['sort_by']." ".$aTableParams['sort_type'];
			if(isset($aTableParams['filter']) && !empty($aTableParams['filter'])  && !is_null($aTableParams['filter']))
				$aReturn['filters'] = $aTableParams['filter'];

//		print_r($aTableParams);
			if(isset($aTableParams['startDate']) && isset($aTableParams['endDate']))
				$aReturn['date'] = array(
					array(
						's'=> $aTableParams['startDate'],
						'e'=> $aTableParams['endDate'],
					)
				);
		}
//		print_r($aReturn);
		return $aReturn;	
	}
}
