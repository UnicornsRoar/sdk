<?php
class VideosModel extends Model{

	/**
	 * 获取活动视频信息
	 * @param  int $eventId
	 * @return array|null
	 */
	public function getEventVideo($eventId){
		return $this->where("event_id = '$eventId'")->order('video_id')->select();
	}
	
	public function addVideos($video){
		$data['account_id']=session('account_id');
		$data['event_id']=$video['event_id'];
		$data['video_title']=$video['vname'];
		$data['video_img']=$video['img'];
		$data['video_object']=$video['object'];
		$data['video_url']=$video['vurl'];
		$result = $this->add($data);
		if(!$result){return false;}
		else{return $result;}
	}
	
	function saveVideo($video){
		$data['account_id']=session('account_id');
		$data['event_id']=$video['event_id'];
		$data['video_title']=$video['vname'];
		$data['video_img']=$video['img'];
		$data['video_object']=$video['object'];
		$data['video_id']=$video['video_id'];
		$data['video_url']=$video['vurl'];
		$result=$this->save($data);
		if(!$result){return false;}
		else{return true;}
	}
	
	function delVideo($video_id){
		$video_id=(int)$video_id;
		if ($this->delete($video_id)) {
			return true;
		} else {
			return false;
		}
	}
	
	function selVideo($video_id){
		$video_id = (int)$video_id;
		$result = $this->find($video_id);
		if($result)  return $result;
		else return false;
	}
	
	function updateVideo($data){
		if(false===$model->create()){
			$this->error($model->getError());
		}
		if(false!==$model->save($data)){
			$this->redirect('manage');
		}else{
			$this->error(L('更新失败'));
		}
	}

	/**
	 * 获取管理页面的视频记录
	 * @param  int  $page
	 * @param  int  $limit
	 * @return array|null|false 成功则返回二维数组
	 */
	public function getAdminVideos($page,$limit=15){
		$result = $this->page("$page,$limit")->order('video_id')->select();
		foreach ($result as $key => $value) {
			$result[$key]['user_name'] = $this->table('sdk_accounts')->where("account_id = {$value['account_id']}")->getField('user_name');
			$result[$key]['event_name'] = $this->table('sdk_events')->where("event_id = {$value['event_id']}")->getField('event_name');
		}
		return $result;
	}
}