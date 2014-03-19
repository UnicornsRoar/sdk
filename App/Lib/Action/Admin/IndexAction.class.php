<?php
class IndexAction extends Action{
	/**
	 * 默认登录
	 */
	public function index(){
		$this->display('login');
	}

	/**
	 * 处理登录
	 */
	public function doLogin(){
		$email    = $_POST['name'];
		$password = $_POST['password'];
		$account  = D('Accounts');
		$valid    = $account->checkAdmin($email,$password);
		switch ($valid) {
			case PWD_WRONG:
				$this->error('密码错误！');
				break;
			case NOT_FOUND:
				$this->error('不存在该用户！');
				break;
			case ERROR:
				$this->error('系统出错');
				break;
			case SUCCESS:
				if ($_SESSION['verify'] == md5(strtolower($_POST['check_word']))){
					$adminInfo = $account->getAdminInfo($email);
					$_SESSION['is_admin'] = 1;
					$_SESSION['username'] = $adminInfo['user_name'];
					$this->redirect('Index/admin');
				} else {
					$this->error('验证码错误');
				}
				break;
		}
	}

	/**
	 * 进入管理页面
	 */
	public function admin(){
		if ($_SESSION['is_admin'] != 1){
			$this->error('你没有权限查看该页面,请登录');
		} else {
			$this->display();
		}
	}
}