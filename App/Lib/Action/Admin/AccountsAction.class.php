<?php
/**
 * 后台用户管理Action
 */
class AccountsAction extends ComAccountsAction{
	public function __construct() {
		parent::__construct();
		//判断是否已登录
		if ($_SESSION['is_admin'] != true)
			$this->error('你没有权限访问该网页！');
	}

	/**
	 * 查看所有用户
	 */
	public function allAccounts(){
		$model = M('Accounts');
		$page   = isset($_GET['p'])?$_GET['p']:1;
		$p = $this->getPages($model->count());
		$field = 'account_id,email,user_name,reg_time,verified';
		$accounts = $model->field($field)->page($page.',15')->order('account_id DESC')->select();
		$this->assign('accounts',$accounts);
		$this->assign('page',$p);
		$this->display();
	}

	/**
	 * 批量删除用户
	 */
	public function deleteInBatch(){
		$model = M('Accounts');
		$condition = array('account_id'=>array('in',$_POST['accounts']));
		if (false!==$model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 删除单个用户
	 */
	public function delete(){
		$model = M('Accounts');
		$id = $this->_get('aid');
		$condition = "account_id = '$id'";
		if (false !== $model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	public function down(){
		$model = M('Accounts');
		$data['account_id'] = $this->_get('aid');
		$data['verified'] = 0;
		$result = $model->save($data);
		if ($result === 1){
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}

	public function up(){
		$model = M('Accounts');
		$data['account_id'] = $this->_get('aid');
		$data['verified'] = 1;
		$result = $model->save($data);
		if ($result === 1){
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}
}