<?php
/**
 * 公共函数文件
 */	

/**
 * 检查权限，用户自身或管理员
 * @param  int $userId 用户的ID
 * @return Bool 是否真有其权限
 */
function check_privilege($userId,$self=false){
	if ($userId === $_POST['account_id'])
		return true;
	if ($self){ 
		//如果要求一定是本人，则返回false
		return false;
	}else{
		// 管理员也有权限
		$result = M('Accounts')->field('is_admin')->find($userId);
		return ($result['is_admin'] == 1);
	}
}

function get_css($array = array()) {
    if (!is_null($array)) {
        $css_href = '';
        foreach ($array as $css) {
            $css_href .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />' . "\n";
        }
        echo $css_href;
    }
}

function htmlspecialchars_deep($var){
	$var = is_array($var) ? array_map('htmlspecialchars_deep', $var) : htmlspecialchars($var);
	return $var;
}