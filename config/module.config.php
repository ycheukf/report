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
			'exportHandler' => $fusionpath.'/ExportHandlers/PHP/FCExporter.php',	//导出文件的的句柄
			'bgSWF' => $fusionpath."/bg-themeblue1.png",
			'exportCallback'=> 'myCallBackFunction',	//回调JS方法
			'exportCallpdf' => 'test/ajaxpdf.php',
			'splitChar' => '__split__',//用于分割的字符串
			'trend_step' => 6,//趋势图时, 最多出现多少个下标
			//'jspath' => 'http://10.0.3.219/team/feng/fusion/JSClass/FusionCharts.js',//趋势图时, 最多出现多少个下标
			'src' => array(
				'pie3D' => $fusionpath.'/FusionCharts/Pie3D.swf',//3d 饼图
				'Bar2d' => $fusionpath.'/FusionCharts/Bar2D.swf',//横向柱状图
				'SSGrid' => $fusionpath.'/FusionCharts/SSGrid.swf',
				'MSCombi2D' => $fusionpath.'/FusionCharts/MSCombi2D.swf',//普通趋势图
				'MSCombiDY2D' => $fusionpath.'/FusionCharts/MSCombiDY2D.swf', //多指标图
				'Bubble' => $fusionpath.'/FusionCharts/Bubble.swf', //bubble图
				'MultiAxisLine' => $fusionpath.'/FusionCharts/MultiAxisLine.swf', //多指标图
				'FCMap_WorldwithCountries' => $fusionpath.'/FusionMaps/FCMap_WorldwithCountries.swf', //世界图
				'FCMap_China2' => $fusionpath.'/FusionMaps/FCMap_China2.swf', //中国图
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
			'host' => 'smtp.sina.com', //smtp服务器地址，可以用ip地址或者域名 
			'port' => '25',
			'auth' => true, //true表示smtp服务器需要验证
			'username' => 'beeradio@sina.com',//用户名
			'password' => 'qwe123', //密码 
		),
		'cache' => array(
			'type'=>'zf2cache',//file代表pear cache,mmc代表memcache
			'file_mem_path'=>'php', //如果是文件存储时可以选择存储目录 php 或 db
			'mmc'=>array(//memcache 连接服务器配置
					'host'			=>'10.0.3.219',
					'port'			=>11211,
					'weight'		=> '1',
					'persistent'	=> FALSE
					),
			'php' => array(//php cache 缓存文件路径设置
					'cacheDir' => CACHE_PATH_ALYS."/php/",
					'lifeTime' => 7200,//两小时
			),
			'csv' => array(//php cache 缓存文件路径设置
					'cacheDir' => CACHE_PATH_ALYS."/csv/",
					'lifeTime' => 7200,//两小时
			),
			'db' => array(//db cache 缓存文件路径设置
					'cacheDir' => CACHE_PATH_ALYS."/db/",
					'lifeTime' => 7200,//两小时
			) ,
			'report_static_html' => array(//报表静态缓存 文件路径设置
					'cacheDir' => CACHE_PATH_ALYS."/report_static_html/",
					'lifeTime' => 604800,//一周
			),
			'shell' => array(//db cache 缓存文件路径设置
					'cacheDir' => CACHE_PATH_ALYS."/shell/",
					'lifeTime' => 7200,//两小时
			) ,		
		),
	),
);