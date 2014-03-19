<?php
// 定义ThinkPHP框架路径
define('THINK_PATH', '../System/');

// 定义项目名称和路径
define('APP_NAME', 'UcApi');
define('APP_PATH', './UcApi/');
define('APP_DEBUG', true);

// ucenter配置文件
require("../uc_config/config.inc.php");
// ucenter客户端入口
require("../uc_client/client.php");
// 加载框架入口文件
require(THINK_PATH . "ThinkPHP.php");

?>