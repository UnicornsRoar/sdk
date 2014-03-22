<?php
class Table_fieldModel extends Model{
	public function addArray($fields, $event_id){
		foreach ($fields as $key => $value) {
			$data[] = array(
				'field_name' => $value['field_name'],
				'is_long' => $value['is_long'],
				'event_id' => $event_id
			);
		}
		return $this->addAll($data);
	}

	/**
	 * 保存字段编辑
	 * @param  array $fields
	 * @param  int $event_id
	 * @return int
	 */
	public function saveArray($fields, $event_id){
		$n = 0;
		foreach ($fields as $key => $value) {
			// 判断是否有新的字段
			if ($value['field_id'] == '-1'){ // 新的记录id标记为-1
				$data = array(
					'field_name' => $value['field_name'],
					'is_long' => $value['is_long'],
					'event_id' => $event_id
				);
				if ($this->add($data) > 0)
					$n++;
			}else{
				$n += $this->save($value);
			}
		}
		return $n;
	}

	public function getEventFields($event_id){
		$fields = $this->where("event_id = '$event_id'")->select();
		return $fields;
	}


	/**
	 * 获取某个人的一个报名
	 * @param  int $event_id
	 * @param  int $account_id [description]
	 * @return array
	 */
	public function getOneRecord($event_id, $account_id){
		$valid_field = $this->field('field_id')->where("event_id = '$event_id")->select();
		$valid_id = array();
		foreach ($valid_field as $key => $value) {
			$valid_id[] = $value['field_id'];
		}
		$where = array('account_id' => $account_id, 'field_id' => array('in', $valid_id));
		$record = M('Field_record')->where($where)->select();
		return $record;
	}
}