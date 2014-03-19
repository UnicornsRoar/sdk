<?php
class AttachmentsAction extends Action {
	public function downLoad(){
		$attachId = $this->_get('f');
		$Model = D('Attachments');
		if ($Model->fileExists($attachId))
			$Model->doFileDown($attachId);
		else
			$this->error('该文件不存在');
	}

	public function del(){
		$attachId = $this->_get('f');
		$Model = D('Attachments');
		if ($Model->fileExists($attachId)){
			$Model->deleteFile($attachId);
			redirect($_SERVER['HTTP_REFERER']);
		}
		else
			$this->error('该文件不存在');
	}

	public function upload(){
		$ac = new EventsAction();
		$info = $ac->upfile(1);
		$data['account_id'] = $_SESSION['account_id'];
		$data['event_id'] = $_POST['event_id'];
		$data['attachment_name'] = $info[0]['name'];
		$data['attachment_path'] = $info[0]['savepath'].$info[0]['savename'];
		$model = M('Attachments');
		$model->add($data);
		$model->table('sdk_events')->where("event_id = '{$data['event_id']}'")->setField('has_attachments',1);
		redirect($_SERVER['HTTP_REFERER']);
	}
}