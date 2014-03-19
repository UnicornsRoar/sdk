/**
 * 检查是否合法的邮箱格式
 *
 * @para string email 用户填写的邮箱
 */
function isValidEmail(email) {
	var pattern = /^([\w\.\_]{1,})@\w+(\.\w+)+$/;
	if (!pattern.test(email)) {
		return false;
	} else {
		return true;
	}
}

/**
 * 检查密码的合法性
 *
 * @para string password 密码
 */
function isValidPassword(password) {
	var pattern = /^[a-zA-Z0-9]{6,16}$/;
	if (!pattern.test(password)) {
		return false;
	} else {
		return true;
	}
}

/**
 * 检查表单的值是否为空
 *
 * @para string value 表单的值
 */
function isNotNull(value) {
	value = $.trim(value);
	if (value != '') {
		return true;
	} else {
		return false;
	}
}