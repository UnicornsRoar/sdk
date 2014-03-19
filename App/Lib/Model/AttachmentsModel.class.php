<?php
class AttachmentsModel extends Model{

	/**
	 * 获取活动附件信息
	 * @param  int $eventId
	 * @return array|null
	 */
	public function getEventAttachment($eventId){
		return $this->where("event_id = '$eventId'")->select();
	}

	public function fileExists($id){
		$address = $this->field('attachment_path')->find($id);
		return file_exists($address['attachment_path']);
	}

	public function doFileDown($id){
		$file = $this->find($id);
		$address = $file['attachment_path'];
		header('Content-Type: application/octet-stream');
		header("Content-Type:application/force-download");
		// header('Accept-Ranges:bytes');
		// header('Accept-Length:'.filesize($address));
		header("Content-Disposition:attachment; filename=".$file['attachment_name']);
		readfile($address);
	}

	public function deleteFile($id){
		$temp = $this->field('attachment_path')->find($id);
		$address = $temp['attachment_path'];
		$this->delete($id);
		return unlink($address);
	}

	public function getAdminAttachments($page,$limit=15){
		$result = $this->page("$page,$limit")->order('attachment_id')->select();
		foreach ($result as $key => $value) {
			$result[$key]['user_name'] = $this->table('sdk_accounts')->where("account_id = {$value['account_id']}")->getField('user_name');
			$result[$key]['event_name'] = $this->table('sdk_events')->where("event_id = {$value['event_id']}")->getField('event_name');
		}
		return $result;
	}
}