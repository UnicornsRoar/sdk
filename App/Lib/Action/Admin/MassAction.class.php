<?php
/**
 * 活动杂项类（评论、附件、视频）
 * @author Don
 */
class MassAction extends ComAccountsAction{
	
	public function __construct() {
		parent::__construct();
		//判断是否已登录
		if ($_SESSION['is_admin'] != 1)
			$this->error('你没有权限访问该网页！');
	}

	/**
	 * 删除一个记录
	 */
	public function delete(){
		$model     = M($this->_get('model'));
		$id        = $this->_get('id');
		$pk        = $this->_get('pk');
		$condition = "$pk = '$id'";
		if ($pk == 'attachment_id'){
			// 删除附件时要特殊处理
			$model = D('Attachments');
			if (!($model->fileExists($id) && $model->deleteFile($id))){
				$model->where($condition)->delete();
				$this->error('删除失败:文件不存在');
			}
		}
		if (false !== $model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 批量删除，删除附件时要区分
	 */
	public function deleteBatch(){
		$pk        = $_POST['pk'];
		$model     = M($_POST['model']);
		$condition = array($pk=>array('in',$_POST['ids']));
		if ($pk == 'attachment_id'){
			// 删除附件时要特殊处理
			$att = $model->where($condition)->select();
			foreach ($att as $key => $value) {
				$address = '.'.$value['attachment_path'];
				if (!(file_exists($address)&&unlink($address)))
					$model->delete($value['attachment_id']);
					$this->error('删除失败:文件不存在');
			}
		}
		if (false!==$model->where($condition)->delete()){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 展示所有评论
	 */
	public function allComments(){
		$model = D('Comments');
		$page  = isset($_GET['p'])?$_GET['p']:1;
		$p = $this->getPages($model->count());
		$comments = $model->getAdminComments($page);
		$this->assign('page',$p);
		$this->assign('comments',$comments);
		$this->display();
	}


	/**
	 * 展示所有附件记录
	 */
	public function allAttachments(){
		$model       = D('Attachments');
		$page        = isset($_GET['p'])?$_GET['p']:1;
		$p           = $this->getPages($model->count());
		$Attachments = $model->getAdminAttachments($page);
		$this->assign('page',$p);
		$this->assign('attachments',$Attachments);
		$this->display();
	}

	/**
	 * 展示所有视频记录
	 */
	public function allVideos(){
		$model  = D('Videos');
		$page   = isset($_GET['p'])?$_GET['p']:1;
		$p      = $this->getPages($model->count());
		$Videos = $model->getAdminVideos($page);
		$this->assign('page',$p);
		$this->assign('videos',$Videos);
		$this->display();
	}
}