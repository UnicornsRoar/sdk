<?php
/**
 * 用户表模型
 * @author Don
 */
//各种状态代码
define('ERROR', 0);
define('SUCCESS', 1);
define('PWD_WRONG', 2);
define('NOT_FOUND', 3);

class AccountsModel extends Model{
	/**
	 * 获取用户表字段
	 * @var string
	 */
	private $_fields = 'account_id,email,user_name,password,department,reg_time,verified';

	/**
	 * 自动验证
	 * @var array
	 */
	protected $_validate = array(
		array('email', 'email', '邮箱地址不正确。'),
		array('email', '' ,'邮箱已被注册。',0,'unique'),
		array('user_name','require','用户名不能为空。'),
		array('user_name', '', '用户名已被注册。', 0, 'unique'),
		array('password','require','密码不能为空。'),
		array('password','/^([a-zA-Z0-9]|[_]){6,18}$/','密码不能为空。','regex'),
		array('comfirm_psw','password','两个密码不一致！',0,'comfirm'),
		array('verify','require','验证码不能为空')
	);

	/**
	 * 自动完成
	 * @var array
	 */
	protected $_auto = array(
	);

	/**
	 * 获取用户信息
	 * @param  int|string $key 用户email或ID
	 * @return array|false|null  若查询成功返回array
	 */
	public function getUserInfo($key,$verified=0){
		if (is_int($key)){
			return ($this->field($this->_fields)->where(array('verified'=>$verified))->find($key));
		}elseif (is_string($key)) {
			return ($this->field($this->_fields)->where(array('email' => $key,'verified'=>$verified))->find());
		}
	}

	/**
	 * 检查密码
	 * @param  string $userkey  用户名或ID
	 * @param  string $inputPwd 输入的密码
	 * @return int   			状态码
	 */
	public function checkPassword($userkey,$inputPwd,$verified){
		$user = $this->getUserInfo($userkey,$verified);
		if ($user){
			// 该用户存在
			if ($user['password'] == sha1($inputPwd)){
				return SUCCESS;
			}else{
				return PWD_WRONG;
			}
		}elseif ($user === null) {//该用户不存在
			return NOT_FOUND;
		}else{
			return ERROR;//查询过程出错
		}
	}

	/**
	 * 整入ucenter前登录函数
	 * @return int 登录状态代码：0，1，2，3
	 */
	public function doLogin($userkey,$password,$verified){
		$check = $this->checkPassword($userkey,$password,$verified);
		$user  = $this->getUserInfo($userkey,$verified);
		if ($check == SUCCESS){
			session_start();
			$_SESSION['account_id'] = $user['account_id'];
			$_SESSION['user_name']  = $user['user_name'];
			$_SESSION['verified'] = 1;
		}
		return $check;
	}

	/**
	 * 整合ucenter后的登录代码,写入SESSION
	 * @param int $account_id
	 * @param string  $user_name
	 */
	public function setLogin($account_id,$user_name){
		$_SESSION['account_id'] = $account_id;
		$_SESSION['user_name']  = $user_name;
		$_SESSION['verified'] = 0;
	}

	

	/**
	 * 修改信息
	 * @return int 状态码
	 */
	public function changeInfo(){

	}

	/**
	 * 修改密码k
	 * @param  int    $userId 
	 * @param  string $newPwd 新密码
	 * @return null
	 */
	public function changePassword($userId,$newPwd){
		$newPwd = sha1($newPwd);
		$this->where($userId)->setField('password',$newPwd);
	}


	/*
	 * 检查邮箱是否被注册
	 */
	function isRegisteredEmail($email,$verified) {
        $count = $this->field(array('account_id'))
            ->where(array('email' => $email,'verified'=>$verified))
            ->count();

        return $count;
    }

	/*
     * 检查社团名是否已经被注册
     */
    function isRegisteredUserName($user_name) {

        $count = $this->field(array('account_id'))
            ->where(array('user_name' => $user_name))
            ->count();
        return $count;
    }

	/**
     * 检查用户名是否合法
     *
     * @para string $user_name 用户输入的用户名
     * @return bool
     */
    function checkUserName($user_name) {
        $user_name = trim($user_name);
        if (preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u', $user_name)) {
            $str_len = strlen(iconv('UTF-8', 'GBK', $user_name));
            if ($str_len >= 4 && $str_len <= 14) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        
    }

    /**
     * 通过ID获取用户名
     * @param  int $id 
     * @return string
     */
   	public function getUserName($id){
   		return $this->where("account_id = $id")->getField('user_name');
   	}

   	/**
   	 * 批量获取用户名字
   	 * 引用并返回$array数组参数
   	 * @param  array $array 每个字数组带有account_id值的数组
   	 * @return array
   	 */
   	public function getUserNameInBatch($array){
   		foreach ($array as $key => $value) {
   			$array[$key]['user_name'] = $this->getUserName($value['account_id']);
   		}
   		return $array;
   	}

   	/**
   	 * 从一个account_id数组中取出用户名
   	 * @param  array $idArray (num_index => acount_id)
   	 * @return array (account_id => user_name)
   	 */
   	public function getUserNameInBatchFromId($idArray){
   		$where = array('account_id'=>array('in', $idArray));
   		$rows = $this->field('account_id, user_name')->where($where)->select();
   		$result = array();
   		foreach ($row as $value) {
   			$result[$value['account_id']] = $value['user_name'];
   		}
   		return $result;
   	}

   	/**
   	 * 获取管理员信息
   	 * @param  string $email
   	 * @return array|null|fales 查询成功则返回数组
   	 */
   	public function getAdminInfo($email){
   		return $this->field($this->_fields)->where("email = '$email' AND is_admin = 1")->find();
   	}

   	/**
   	 * 检查管理员帐号
   	 * @param  string $email
   	 * @param  string $inputPwd
   	 * @return int    状态码
   	 */
   	public function checkAdmin($email,$inputPwd){
   		$info = $this->getAdminInfo($email);
		if ($info){
			// 该用户存在
			if ($info['password'] == sha1($inputPwd)){
				return SUCCESS;
			}else{
				return PWD_WRONG;
			}
		}elseif ($info === null) {//该用户不存在
			return NOT_FOUND;
		}else{
			return ERROR;//查询过程出错
		}
   	}
}
