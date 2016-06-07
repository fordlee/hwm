<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_MODULE' => 'Index', //默认模块
	//URL不区分大小写
    'URL_CASE_INSENSITIVE' => true,
	'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
	'APP_STATUS' => 'debug', //应用调试模式状态
	'TMPL_PARSE_STRING' => array (// 站点公共目录
				'__PUBLIC__' => __ROOT__ . '/Public'
	),

	//打开页面跟踪
    //'SHOW_PAGE_TRACE' => true,
	
	//数据库相关
	'DB_PREFIX' => '',
	'DB_DSN' => 'mysql://root:@localhost:3306/hwm',//数据库配置
	'DB_CHARSET'=>'utf8',
	
	//限制伪静态后缀
	'URL_HTML_SUFFIX'=>'',
	'URL_ROUTER_ON'   => true, //开启路由
	'URL_ROUTE_RULES' => array( //定义路由规则
	)
);
?>