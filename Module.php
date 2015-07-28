<?php
namespace YcheukfReport;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
		$aConfig = include __DIR__ . '/config/module.config.php';
		\YcheukfReport\Lib\ALYS\ALYSConfig::setAll($aConfig['YcheukfReport']);
        return $aConfig;
    }
	public function onBootstrap(MvcEvent $event){
//        if (PHP_SAPI === 'cli') return;
		$app = $event->getApplication();
		$sm  = $app->getServiceManager();

		\YcheukfReport\Lib\ALYS\ALYSConfig::set('dbHandle', $sm->get('Zend\Db\Adapter\Adapter'));
		\YcheukfReport\Lib\ALYS\ALYSConfig::set('smHandle', $sm);

	}

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/Report',
                ),
            ),
        );
    }
	public function getServiceConfig(){
        return array(
            'invokables' => array(
                'YcheukfReportService'	=> 'YcheukfReport\Service',
			),
		);
	
	}

}
