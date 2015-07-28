<?php
defined('CACHE_PATH_ALYS') or define('CACHE_PATH_ALYS', __DIR__.'/../../../../data/ycfreport');
$fusionpath = 'public/js/FCPHPClassCharts';

$fusionpath = 'js/FCPHPClassCharts';
return array(

    'controllers' => array(
        'invokables' => array(
            'YcheukfReport\Controller\DemoController' => 'YcheukfReport\Controller\DemoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'ycfreport' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/ycfreport[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'YcheukfReport\Controller',
                        'controller' => 'DemoController',
                        'action' => 'index',
                    ),

//                'type' => 'segment',
//                'options' => array(
//                    'route' => '/ycfreport',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'YcheukfReport\Controller',
//                        'controller' => 'IndexController',
//                        'action' => 'index',
//                    ),
                ),
                'may_terminate' => true,
//                'child_routes' => array(
//                    'reportdemo' => array(
//                        'type' => 'segment',
//                        'options' => array(
//                            'route' => '/demo',
//                            'defaults' => array(
//                                'controller' => 'DemoController',
//                                'action' => 'index',
//                            ),
//                        ),
//                    )
//				),
			),
		),
	),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

	'YcheukfReport'=>array(
		'debug' => 0,
		'dateField' => 'date',
		'dboperator' => 'resourceapi',//[sql|resourceapi]
		'fusionpath' => $fusionpath,
		'dbHandle' => 'Zend\Db\Adapter\Adapter',
		'fusion' => array(
			'exportHandler' => $fusionpath.'/ExportHandlers/PHP/FCExporter.php',	//�����ļ��ĵľ��
			'bgSWF' => $fusionpath."/bg-themeblue1.png",
			'exportCallback'=> 'myCallBackFunction',	//�ص�JS����
			'exportCallpdf' => 'test/ajaxpdf.php',
			'splitChar' => '__split__',//���ڷָ���ַ���
			'trend_step' => 6,//����ͼʱ, �����ֶ��ٸ��±�
			//'jspath' => 'http://10.0.3.219/team/feng/fusion/JSClass/FusionCharts.js',//����ͼʱ, �����ֶ��ٸ��±�
			'src' => array(
				'pie3D' => $fusionpath.'/FusionCharts/Pie3D.swf',//3d ��ͼ
				'Bar2d' => $fusionpath.'/FusionCharts/Bar2D.swf',//������״ͼ
				'SSGrid' => $fusionpath.'/FusionCharts/SSGrid.swf',
				'MSCombi2D' => $fusionpath.'/FusionCharts/MSCombi2D.swf',//��ͨ����ͼ
				'MSCombiDY2D' => $fusionpath.'/FusionCharts/MSCombiDY2D.swf', //��ָ��ͼ
				'Bubble' => $fusionpath.'/FusionCharts/Bubble.swf', //bubbleͼ
				'MultiAxisLine' => $fusionpath.'/FusionCharts/MultiAxisLine.swf', //��ָ��ͼ
				'FCMap_WorldwithCountries' => $fusionpath.'/FusionMaps/FCMap_WorldwithCountries.swf', //����ͼ
				'FCMap_China2' => $fusionpath.'/FusionMaps/FCMap_China2.swf', //�й�ͼ
			)
		
		),
		'pdf' => array(
			'path'		=> CACHE_PATH_ALYS."/pdf/",
			'author'	=> 'allyeser',
			'title'		=> 'allyes pdf page',
			'subject'	=> 'allyes',
			'keyword'	=> 'allyes , pdf',
			'img'		=> 'logo.gif',
			'lifetime'	=> 3600,
			'prefixname'=> '_ALYS_',
			'remove'	=>array('pdf','jpg','png'),
		),
		'email' => array(
			'host' => 'smtp.sina.com', //smtp��������ַ��������ip��ַ�������� 
			'port' => '25',
			'auth' => true, //true��ʾsmtp��������Ҫ��֤
			'username' => 'beeradio@sina.com',//�û���
			'password' => 'qwe123', //���� 
		),
		'cache' => array(
			'type'=>'zf2cache',//file����pear cache,mmc����memcache
			'file_mem_path'=>'php', //������ļ��洢ʱ����ѡ��洢Ŀ¼ php �� db
			'mmc'=>array(//memcache ���ӷ���������
					'host'			=>'10.0.3.219',
					'port'			=>11211,
					'weight'		=> '1',
					'persistent'	=> FALSE
					),
			'php' => array(//php cache �����ļ�·������
					'cacheDir' => CACHE_PATH_ALYS."/php/",
					'lifeTime' => 7200,//��Сʱ
			),
			'csv' => array(//php cache �����ļ�·������
					'cacheDir' => CACHE_PATH_ALYS."/csv/",
					'lifeTime' => 7200,//��Сʱ
			),
			'db' => array(//db cache �����ļ�·������
					'cacheDir' => CACHE_PATH_ALYS."/db/",
					'lifeTime' => 7200,//��Сʱ
			) ,
			'report_static_html' => array(//����̬���� �ļ�·������
					'cacheDir' => CACHE_PATH_ALYS."/report_static_html/",
					'lifeTime' => 604800,//һ��
			),
			'shell' => array(//db cache �����ļ�·������
					'cacheDir' => CACHE_PATH_ALYS."/shell/",
					'lifeTime' => 7200,//��Сʱ
			) ,		
		),
	),
);