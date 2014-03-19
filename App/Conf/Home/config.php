<?php
return array(
	//'配置项'=>'配置值'

	// 数据库配置
	'URL_MODEL' => 2,
	'DB_TYPE'   => 'mysql',
	'DB_HOST'   => 'localhost',
	'DB_NAME'   => 'sdk',
	'DB_USER'   => 'sdk',
	'DB_PWD'    => 'mylocalhost',
	'DB_PORT'   => 3306,
	'DB_PREFIX' => 'sdk_',


	/* 资源路径 */
    'TMPL_PARSE_STRING'     => array(
        '__CSS__' => __ROOT__.'/App/Public/Css',         // css路径
        '__IMG__' => __ROOT__.'/App/Public/Images',      // 图片路径
        '__JS__' => __ROOT__.'/App/Public/Js',           // js路径
        '__UPLOAD__' => __ROOT__.'/App/Public/Uploads',  // 上传文件路径
        '__LOGIN__' => U('Accounts/doLogin'),
    ),

    /* URL设置 */
	'URL_ROUTER_ON'         => true , // 是否开启URL路由
	 'URL_MODEL'      => 2,     // PATHINFO 模式
    'URL_PATHINFO_MODEL'    => 2,       //设置为智能模式
    'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置

	 // 上传文件目录物理地址
    'UPLOADS_ADDR' => str_replace('\\', '/', (dirname(dirname(__FILE__)))) . '/Public/Uploads',
    // 上传文件目录网站绝对路径
    'UPLOADS' => __ROOT__.'/App/Public/Uploads',
	'__LOGIN__' => U('Accounts/doLogin'),
    'IMG' => __ROOT__.'/APP/Public/Images',
	// 域名代替
	'HTTP_HOST' => 'http://www.suidaokou.com',
	'HTTP_HOST2' => 'http://suidaokou.com',
	// 根目录路径
	'DOCUMENT_ROOT' => dirname(dirname(dirname(dirname(dirname(__FILE__))))),
);
?>