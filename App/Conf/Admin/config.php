<?php
return array(
	//'配置�?=>'配置�?
	'URL_MODEL'=> 2,
	'APP_DEBUG' =>true,
		
	// 数据库设�?
    'DB_TYPE' => 'mysql', // 数据库类�?
    'DB_HOST' => 'localhost', // 数据库朋务器地址
    'DB_NAME' =>'sdk', // 数据库名�?
    'DB_USER' =>'sdk_db', // 数据库用户名
    'DB_PWD' =>'quanta_2012_sdk', // 数据库密�?
    'DB_PORT' =>'3306', // 数据库端�?
    'DB_PREFIX' =>'sdk_', // 数据表前缀
	
	// 资源路径
	'TMPL_PARSE_STRING'     => array(
		'__CSS__' => __ROOT__.'/App/Public/Css',         // css路径
        '__IMG__' => __ROOT__.'/App/Public/Images',      // 图片路径
        '__JS__' => __ROOT__.'/App/Public/Js',           // js路径
        '__UPLOAD__' => __ROOT__.'/App/Public/Uploads',  // 上传文件路径
	),
	
	/* URL设置 */
	'URL_CASE_INSENSITIVE'  => false,   // URL地址是否不区分大小写
    'URL_ROUTER_ON'         => true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       => array(), // 默认路由规则，注：分组配置无法替�?
);
?>