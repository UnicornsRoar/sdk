<?php
class HotsModel extends Model{
	public function getHot(){
		$where = "off_time = 0";
		$field1 = 'event_id,banner_img';
		$result = $this->field($field)->where($where)->order('begin_time desc')->select();
		$field2 = 'event_host,event_locale,start_time';
		foreach ($result as $key => $value) {
			$event = $this->table('sdk_events')->field($field2)->where("event_id = '{$value['event_id']}'")->find();
			$result[$key] = array_merge($result[$key],$event);
		}
		return $result;
	}
}