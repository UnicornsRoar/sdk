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
	 * 判断活动是否有设置过报名表
	 * @param  int $event_id
	 * @return boolean
	 */
	public function hasSetTable($event_id){
		$count = $this->where("event_id = $event_id")->count();
		return ($count) ? true : false;
	}

	/**
	 * 判断用户是否已经报名
	 * @param  int  $event_id  
	 * @param  int  $account_id
	 * @return boolean
	 */
	public function hasUserSign($event_id, $account_id){
		$fields = $this->getEventFields($event_id);
		$fid = array();
		foreach ($fields as $value) {
			$fid[] = $value['field_id'];
		}
		$where = array(
				'event_id'   => $event_id,
				'account_id' => $account_id,
				'field_id'   => array('in', $fid)
			);
		$result = M('Field_record')->where($where)->find() ? true : false;
		return $result;
	}


	/**
	 * 获取一个用户在一个活动中的报名信息
	 * @param  int $event_id
	 * @param  int $account_id [description]
	 * @return array
	 */
	public function getOneRecord($event_id, $account_id){
		$valid_field = $this->field('field_id, field_name, is_long')->where("event_id = '$event_id'")->select();
		$valid_id    = array();
		$id2name     = array();
		foreach ($valid_field as $key => $value) {
			$valid_id[] = $value['field_id'];
			$id2name[$value['field_id']] = array(
					'field_name' => $value['field_name'],
					'is_long' => $value['is_long']
				);
		}

		$where = array('account_id' => $account_id, 'field_id' => array('in', $valid_id));
		$record = M('Field_record')->where($where)->select();
		foreach ($record as &$value) {
			$value['field_name'] = $id2name[$value['field_id']]['field_name'];
			$value['is_long'] = $id2name[$value['field_id']]['is_long'];
		}
		return $record;
	}

	/**
	 * 确认recordIds是否都为同一个用户
	 * @param  array $recordIds (index => record_id)
	 * @return fasle | 同一个用户的ID
	 */
	public function confirmOneUser($recordIds){
		if (count($recordIds)==0)
			return fasle;
		$range = implode(',', $recordIds);
		$sql = "SELECT DISTINCT `account_id` FROM sdk_field_record WHERE record_id IN ($range)";
		$model = new Model();
		$result = $model->query($sql);
		if (count($result) != 1){
			return false;
		}else{
			return $result[0]['account_id'];
		}
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

	public function getAllSignUsers($event_id){
		$sql    = "SELECT DISTINCT `account_id` FROM sdk_field_record WHERE event_id='$event_id'";
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
		$nowField = $this->getEventFields($event_id);
		if (count($nowField) == 0)
			return 0;
		foreach ($nowField as $value) {
			$fields[] = $value['field_id'];
		}
		$fieldsStr = implode(',', $fields);
		$sql    = "SELECT DISTINCT `account_id` FROM sdk_field_record WHERE event_id='$event_id' AND field_id IN ($fieldsStr)";
		$model  = new Model();
		$result = $model->query($sql);
		return count($result);
	}

	/**
	 * 构造已填信息的表
	 * @param  array $fields  (index => array(''=>v2,''=>v2...))
	 * @param  array $usersId (index => account_id)
	 * @return array 2维数组
	 */
	public function createTable($fields, $usersId){
		// 把字段ID独立组装成数组
		$fieldsId = array();
		foreach ($fields as $value) {
			$fieldsId[] = $value['field_id'];
		}

		$table = array();
		$recordModel = M('Field_record');
		// 每次获取一个用户所填信息
		foreach ($usersId as $account_id) {
			$where = array('account_id'=>$account_id, 'field_id'=>array('in', $fieldsId));
			$accountFields = $recordModel->field('field_id, content')->where($where)->order('field_id')->select();
			// 填充表格中的每一格
			foreach ($accountFields as $oneField) {
				// 中间第二维是为了确保遍历时的顺序是按照field_id排序
				$table[$account_id][$oneField['field_id']] = $oneField;
			}

			// 检查是否有空的字段，如果为空，则填上“空”
			foreach ($fieldsId as $fkey) {
				if (!isset($table[$account_id][$fkey]))
					$table[$account_id][$fkey] = array('content' => "空");;
			}
			ksort($table[$account_id]);
		}
		return $table;
	}
}