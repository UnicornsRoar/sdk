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
			if ($value['field_id'] == -1){ // 新的记录id标记为-1
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

	/**
	 * 获取整个活动设置了的字段
	 * @param  int $event_id
	 * @return array|null|fales
	 */
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

	/**
	 * 获取在一个活动中已经报名的用户的id
	 * @param  integer $event_id
	 * @param  integer $page    
	 * @param  integer $limit   
	 * @return array
	 */
	public function getSignUsers($event_id, $page=1, $limit=15){
		$begin  = ($page-1)*$limit;
		$sql    = "SELECT DISTINCT `account_id` FROM sdk_field_record WHERE event_id='$event_id' LIMIT $begin,15";
		$model  = new Model();
		$result = $model->query($sql);
		$users  = array();
		foreach ($result as $key => $value) {
			$users[] = $value['account_id'];
		}
		return $users;
	}

	/**
	 * 统计一个活动有多少人报名
	 * @param  int $event_id
	 * @return int
	 */
	public function getSignCount($event_id){
		$sql    = "SELECT DISTINCT `account_id` FROM sdk_field_record WHERE event_id='$event_id'";
		$model  = new Model();
		$result = $model->query($sql);
		return count($result);
	}

	/**
	 * 构造已填信息的表
	 * @param  array $fields  (index => array(''=>v2,''=>v2...))
	 * @param  array $usersId (index => account_id)
	 * @return array 三维数组
	 */
	public function createTable($fields, $usersId){
		$fieldsId = array();
		foreach ($fields as $value) {
			$fieldsId[] = $value['field_id'];
		}

		$table = array();
		// 每次获取一个用户所填信息
		foreach ($usersId as $account_id) {
			$where = array('account_id'=>$account_id, 'field_id'=>array('in', $fieldsId));
			$accountFields = $this->table('sdk_field_record')->where($where)->order('field_id')->select();
			// 填充表格中的每一格
			foreach ($accountFields as $oneField) {
				// 中间第二维是为了确保遍历时的顺序是按照field_id排序
				$table[$account_id][$oneField['field_id']] = $oneField;
			}
		}
		return $table;
	}
}