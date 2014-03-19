<?php
return array(
	/* 项目设定 */
    'APP_DEBUG' => false, // 开启调试模式
    
	//'配置项'=>'配置值'
	'APP_GROUP_LIST' => 'Home,Admin',
	'DEFAULT_GROUP'  => 'Home',
	'TMPL_VAR_IDENTIFY' => 'array',

	/* 模板引擎设置 */
    'TMPL_CACHE_ON'			=> false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    
    /* 表单令牌验证 */
    'TOKEN_ON'              => false,     // 开启令牌验证
    
    /* URL设置 */
	'URL_CASE_INSENSITIVE'  => false,   // URL地址是否不区分大小写
    'URL_ROUTER_ON'         => true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       => array(), // 默认路由规则，注：分组配置无法替代
    //'URL_DISPATCH_ON'       => true,	// 是否启用Dispatcher，不再生效
    'URL_MODEL'      => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_MODEL'    => 2,       // PATHINFO 模式,使用数字1、2、3代表以下三种模式:
    // 1 普通模式(参数没有顺序,例如/m/module/a/action/id/1);
    // 2 智能模式(系统默认使用的模式，可自动识别模块和操作/module/action/id/1/ 或者 /module,action,id,1/...);
    // 3 兼容模式(通过一个GET变量将PATHINFO传递给dispather，默认为s index.php?s=/module/action/id/1)
    'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    //'URL_AUTO_REDIRECT'     => true, // 自动重定向到规范的URL 不再生效
    
    /* 自定义常量 */
    // 网站名称
    'SITE_NAME' => '隧道口',
    // 上传文件目录物理地址
    'UPLOADS_ADDR' => str_replace('\\', '/', (dirname(dirname(__FILE__)))) . '/Public/Uploads',
    // 上传文件目录网站绝对路径
    'UPLOADS' => __ROOT__.'/App/Public/Uploads',
    'IMG' => __ROOT__.'/APP/Public/Images',
	// 域名代替
	'HTTP_HOST' => 'http://www.suidaokou.com',
	'HTTP_HOST2' => 'http://suidaokou.com',	
);
?>