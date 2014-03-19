<?php
class EventsAction extends ComEventAction{

	public function __construct() {
		parent::__construct();
		//判断是否已登录
		if ($_SESSION['is_admin'] !== 1)
			$this->error('你没有权限访问该网页！');
	}

	/**
	 * 展示所有活动
	 */
	public function allEvents(){
		$eventModel = D('Events');
		$page   = isset($_GET['p'])?$_GET['p']:1;
		$events = $eventModel->adminGetEventDetails($page);
		$p      = $eventModel->getPages($eventModel->count());
		$this->assign('page',$p);
		$this->assign('events',$events);
		$this->display();
	}

	/**
	 * 批量删除活动
	 */
	public function deleteBatch(){
		$model = M('Events');
		$condition = array('event_id'=>array('in',$_POST['ids']));
		if (false!==$model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 删除单个活动
	 */
	public function delete(){
		$model     = M('Events');
		$event_id  = $this->_get('e');
		$condition = "event_id = '$event_id'";
		if (false !== $model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 修改活动信息
	 */
	public function edit(){
		$model     = D('Events');
		$event_id  = $this->_get('e');
		$details = $model->getEventDetails($event_id);
		$this->assign('details',$details);
		$this->display();
	}


	/**
	 * 保存活动修改
	 * @return int ajax
	 */
	public function saveEdit(){
		if (isset($_POST['event_id'])){
			$result = M('Events')->save($_POST);
			echo ($result === 1) ? 1 : 0; 
		} else {
			echo 0;
		}
	}
}