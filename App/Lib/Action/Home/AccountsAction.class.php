<?php
class AccountsAction extends ComAccountsAction{
	public function index(){

	}
	/**
	 * 执行登录操作，修改cookies
	 */
	// 整入ucenter前的登录函数
	public function doMassLogin(){
		$Model = D('Accounts');
		$Model->create();
		$verified=1;
		$check = $Model->doLogin($_POST['email'],$_POST['password'],$verified);
		if ($check === SUCCESS){
			if ($_POST['remember'] == 'yes'){
				cookie('loginEmail',$_POST['email'],3600*24*7);
				cookie('loginPassword',$_POST['password'],3600*24*7);
			}
			if ($_POST['isAjax'] == 1){ //Ajax通过cookie自动登录不用刷新页面
				echo 1;
			}else{
				//redirect($_SERVER['HTTP_REFERER']);//刷新页面
			}
			$this->redirect('Index/index');
		}else{
			if ($check === PWD_WRONG)
				$this->ajaxReturn('nothing','密码错误',0);
			else
				$this->ajaxReturn('nothing','用户不存在',0);
		}
	}
	/*public function doLogin(){
		$Model = D('Accounts');
		$Model->create();
		// 提取用户信息
		$email = $_POST['email'];
		$password = $_POST['password'];
		$remember = $_POST['remember'];
		$verified=1;
		//登录ucenter
		list($uid, $username, $password, $email) = uc_user_login($email, $password, 2);

		// 如果返回的uid大于0表示在ucenter登陆成功
		if ($uid > 0){

			// 如果没有在隧道口注册，自动添加
			if (!$Model->isRegisteredEmail($email,$verified)){
				$account['account_id'] = $uid;
				$account['email'] = $email;
				$account['user_name'] = $username;
				$account['password'] = sha1(time());//随机密码
				$account['reg_time'] = time();
				$Model->add($account);
			}

			// 设置cookie,自动登录
			if($remember == 'yes'){
				setcookie('sdk_auth', uc_authcode($email.'\t'.$password, 'ENCODE'), time()+3600*24*365, '/', '.suidaokou.com');
				cookie('loginEmail',$email,3600*24*365);
				cookie('loginPassword',$password,3600*24*365);
			}

			// 隧道口本地登录
			$userInfo = $Model->getUserInfo($email);
			$Model->setLogin($userInfo['account_id'],$userInfo['user_name']);
			// ucenter上其他应用同步登录(输出js代码)
			$ucSynLogin = uc_user_synlogin($uid);

			// 判断是否cookie自动登录
			if($_POST['isAjax'] == 1){
				$this->ajaxReturn($ucSynLogin,'登录成功',1);
			}else{
				echo $ucSynLogin;//输出同步登录js代码
				echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>"';
			}

		}else{
			// 如果ucenter中密码不正确而本地密码正确则放行
			$check = $Model->checkPassword($email,$password);
			if ($check === SUCCESS){
				$userInfo = $Model->getUserInfo($email);
				// 更改ucenter中的密码
				uc_user_edit($username, '', $password, '', 1);
				if ($remember == 'yes'){
					setcookie('sdk_auth', uc_authcode($email.'\t'.$password, 'ENCODE'), time()+3600*24*365, '/', '.suidaokou.com');
				}

				// 本地登录
				$Model->setLogin($userInfo['account_id'],$userInfo['user_name']);
				$ucSynLogin = uc_user_synlogin($uid);

				if($_POST['isAjax'] == 1){
					$this->ajaxReturn($ucSynLogin,'登录成功',1);
				}else{

					//记录cookie
					if($remember == 'yes'){
						setcookie('sdk_auth', uc_authcode($email.'\t'.$password, 'ENCODE'), time()+3600*24*365, '/', '.suidaokou.com');
						cookie('loginEmail',$email,3600*24*365);
						cookie('loginPassword',$password,3600*24*365);
					}

					echo $ucSynLogin;//输出同步登录js代码
					echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>"';
				}
			// ucenter中密码不正确，本地也不正确
			}else{
				if ($check == PWD_WRONG){
					if ($_POST['isAjax'] == 1){
						$this->ajaxReturn('nothing','密码错误',0);
					}else{
						$this->error('密码错误');
					}
				}else{
					if ($_POST['isAjax'] == 1){
						$this->ajaxReturn('nothing','用户不存在',0);
					}else{
						$this->error('用户不存在');
					}
				}
			}
		}
	}*/

	public function doLogin(){
		$Model = D('Accounts');
		$verified = 0;
		$check = $Model->doLogin($this->_post('email'), $this->_post('password'), $verified);
		if ($check == SUCCESS){
			if ($this->_post('remember') == 'yes'){
				cookie('loginEmail',$_POST['email'],3600*24*7);
				cookie('loginPassword',$_POST['password'],3600*24*7);
			}
			$this->redirect('Index/index');
		}else{
			$this->error('登录失败', $_SERVER['HTTP_REFERER']);
		}
	}

	/*
	 * 验证密码是否正确
	 */
	 function isTruePassword(){
		 $Model = D('Accounts');
		$Model->create();
		$check = $Model->doLogin($_POST['email'],$_POST['password']);
		if ($check === SUCCESS){
			$this->ajaxReturn('','',1);
		}else{
			$this->ajaxReturn('','',0);
		}
	 }

	/**
	 * 返回是否已登录
	 */
	public function isLogined(){
		echo ($_SESSION['account_id'] != '' && $_SESSION['user_name'] != '') ? 1 : 0;
	}

	/**
	 * 注销登录，返回首页
	 */
	public function loginOut(){
		cookie('loginEmail','null',-1);
		cookie('loginPassword','null',-1);
		unset($_SESSION['account_id']);
		unset($_SESSION['user_name']);
		echo '<script type="text/javascript">window.location.href="' . U('Index/index') . '"</script>"';
	}

	/**
	 * 我的留言页面
	 */
	public function mymessage(){
		$Model = D('Comments');
		$comments = $Model->getAccountReceiveComment($_SESSION['account_id']);
		foreach ($comments as $key => $value) {
			$reply = $Model->getOneReplyFromComment($value['comment_id']);
			if ($reply)
				$comments[$key]['reply'] = $reply;
		}
		$this->assign('comments',$comments);
		$this->display('mymessage');
	}

	/**
	 * 回复留言
	 */
	public function reply(){
		$Model = D('Comments');
		if ($Model->create()){
			$data['comment'] = $_POST['comment'];
			$data['account_id'] = $_SESSION['account_id'];
			$data['pre_comment_id'] = $_POST['cid'];
			$Model->insertReply($data);
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error($Model->getError());
		}
	}

	/*
	 * 用户注册
	 */
	public function  regist(){
		$js=array(
			'__JS__/validation.js',
			'__JS__/AjaxFileUploaderV2.1/ajaxfileupload.js',
		);
		$this->assign('js',$js);
		$this->display();
	}
	/*
	 * 检查是否为已注册邮箱
	 */
	public function isRegisteredEmail() {
        if (isset($_POST['submitted'])) {
            $email = $_POST['email'];
			$verified = $_POST['verified'];
            $Account = new AccountsModel();
            if ($Account->isRegisteredEmail($email,$verified)) {
                $this->ajaxReturn('', '该邮箱已经被注册', 1);
            } else {
                $this->ajaxReturn('', '', 0);
            }
        }
    }

	/**
     * 检查用户名是否合法
     */
    public function isValidUserName() {
        if (isset($_POST['submitted'])) {
            $user_name = $_POST['user_name'];
            $type = $_POST['type'];
            $Account = new AccountsModel();
            if (!$Account->checkUserName($user_name)) {
                $this->ajaxReturn('', '用户名3-15个字节', 0);
            } else {
                // 如果是编辑资料, 当填写的用户名与当前登录的用户名相同时返回
                // 即没有更改用户时返回
                if ($type == 'edit') {
                    if ($user_name == $_SESSION['account']['user_name']) {
                        $this->ajaxReturn('', '', 2);
                    }
                }
                // 检查用户名是否被注册
                if ($Account->isRegisteredUserName($user_name)) {
                    $this->ajaxReturn('', '该用户名已经被注册', 0);
                } else {
                    $this->ajaxReturn('', '', 1);
                }
            }
        }
    }

	/*
	 * 社团信息录入
	 */
	public function registMass(){
		if(isset($_POST['submitted'])){
			if((empty($_POST['email']))||(empty($_POST['mass']))||(empty($_POST['pass']))||(empty($_POST['poster_src']))){
				$this->error("请填写完整信息");
			}else{
				$event['user_name']=$_POST['mass'];
				$event['email']=$_POST['email'];
				$event['password']=$_POST['pass'];
				$event['poster_src']=$_POST['poster_src'];
				if ($event['poster_src'] == '') {
					$event['poster_src'] = C('IMG') . '/thumbnail/thumbnailPoster.png';
				}
				if (isset($_POST['edit']) == 1) {
					$event['event_id']=$_POST['event_id'];
					$this->update($event);
				}else{
					$this->insert($event);
				}
			}
		}
	}
	/*
	 * 社团登陆
	 */
	function massLogin(){
		$this->assign('type',1);
		$this->display();
	}
	/*
	 * 用户登录
	 */
	function userLogin(){
		$this->assign('type',0);
		$this->display('massLogin');
	}
}
