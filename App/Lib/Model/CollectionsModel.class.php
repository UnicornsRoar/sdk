<?php
class CollectionsModel extends Model{

	/**
	 * 取出活动的被收藏次数
	 * @param  int $eventID 
	 * @return int
	 */
	public function getEventCollectCount($eventId){
		return $this->where("c_event_id = '$eventId'")->count();
	}

	/**
	 * 判断是否已收藏
	 * @param  int  $eventId
	 * @return boolean
	 */
	public function isCollected($eventId){
		$where  = "c_event_id = '$eventId' AND account_id = '{$_SESSION['account_id']}'";
		$result = $this->where($where)->count();
		return ($result > 0)? TRUE : FALSE;
	}
}