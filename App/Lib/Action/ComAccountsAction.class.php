<?php
/**
 * 用户操作公共Action
 * @author Don
 */
class ComAccountsAction extends Action{
	/**
	 * 获取用户信息，目前是以隧道口自身用户为主，以后会开通广外通行证
	 * @param  int $userId 用户的ID
	 * @return array 用户的资料
	 */
	public function getUserInfo ($userId){
		$model = D("Accounts");
		return $model->getUserInfo($userId);
	}

		/*
	 * 基础函数调用：新增操作
	 */
	function insert($data=array()){
		$model=D($this->getActionName());
		if(false===$model->create()){
			$this->error($model->getDbError());
		}
		$result=$model->add($data);
		if($result){
			$this->redirect('Index/index');
		}else{
			$this->error("新增失败");
		}
	}
	
    /**
     * 得到页码列表
     * @param int $count 为内容总数，总共有多少项记录
     *  @return String
     */
    public function getPages($count) {
        import('@.ORG.Page');
        $page   = new Page($count, 15);
        return $page->show();
    }
}