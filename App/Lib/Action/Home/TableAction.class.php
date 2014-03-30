<?php
class TableAction extends Action{

	/**
	 * 判断活动是否属于用户
	 * @param  int $event_id
	 * @param  int $account_id
	 * @return bool
	 */
	private function _checkEventBelong($event_id, $account_id=NULL){
		if ($account_id == NULL)
			$account_id = session('account_id');
		$where = array('event_id' => $event_id, 'account_id' => $account_id);
		if (M('Events')->where($where)->find()){
			return true;
		}else{
			return false;
		}
	}
	/**	
	 * 新建报名表，创建字段
	 */
	public function entryForm(){
		if (IS_POST){
			$event_id = $this->_get('e');
			if ($this->_checkEventBelong($event_id)){
				$pattern = "/^field_name\d+$/";
				foreach ($_POST as $key => $value) {
					// 通过正则表达式匹配变量名
					if (preg_match($pattern, $key) AND !empty($value)){
						// 取后面数字
						$number = substr($key, 10, strlen($key));
						$is_long = ($this->_post('field_long'.$number) == 1)?1:0;
						$data[] = array(
								'event_id' => $event_id,
								'field_name' => $value,
								'is_long' => $is_long
							);
					}
				}
				$result = M('Table_field')->addAll($data);
				if ($result){
					$this->redirect($_SERVER['HTTP_REFERER']);
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error('非法操作！');
			}
		}else{
			$this->display('entryForm');
		}
	}

	/**
	 * 编辑报名表字段
	 */
	public function editEntry(){
		$event_id = $this->_get('e');
		if (!$this->_checkEventBelong($event_id))
			$this->error('非法操作！');
		if (IS_POST){
			$save_pattern = "/^field_id\d+$/";
			$add_pattern = "/^field_name\d+$/";
			foreach ($_POST as $key => $value) {
				// 检查是不是更新的数据
				if (preg_match($save_pattern, $key)){
					$number  = substr($key, 8, strlen($key));
					$is_long = ($this->_post('is_long'.$number) == 1)?1:0;
					$save_data[] = array(
							'field_id'   => $number,
							'field_name' => $this->_post($key),
							'is_long'    => $is_long
						);
				// 检查是不是插入新数据
				}elseif (preg_match($add_pattern, $key) AND !empty($value)) {
					$number  = substr($key, 10, strlen($key));
					$is_long = ($this->_post('field_long'.$number) == 1)?1:0;
					$save_data[] = array(
							'field_id'   => -1,/* -1表明是插入信息 */
							'field_name' => $this->_post($key),
							'is_long'    => $is_long
						);
				}
			}
			// 写入数据库
			$affected_rows = D('Table_field')->saveArray($save_data, $event_id);
			if ($affected_rows)
				$this->success('保存成功');
			else
				$this->error('保存失败');
		}else{
			$fieldModel = D('Table_field');
			$fields = $fieldModel->getEventFields($event_id);
			$this->assign('fields', $fields);
			$this->display('editEntry');
		}
	}

	/**
	 * 用户报名
	 */
    public function submitForm(){
    	if (!session('account_id'))
    		$this->error('请先登录');
    	$fieldModel = D('Table_field');
    	$event_id = $this->_get('e');
    	if (IS_POST){
    		$account_id = session('account_id');
    		foreach ($_POST as $key => $value) {
    			$data[] = array(
    					'field_id' => $key,
    					'content' => $this->_post($key),
    					'account_id' => $account_id,
    					'event_id' => $event_id
    				);
    		}
    		if ($fieldModel->addAll($data))
    			$this->success('报名成功');
    		else
    			$this->error('报名失败');
    	}else{
	    	$fields = $fieldModel->getEventFields($event_id);
	    	if (empty($fields))
	    		$this->error('无报名表设置');
    		$this->assign('fields', $fields);
			$this->display('submitForm');
    	}
	}

	public function add(){
		$fields = $this->_post('fields', 'htmlspecialchars_deep');
		if(IS_POST and isset($fields)){
			$result = D('Table_field')->addArray($fields, $this->_post('event_id'));
			if ($result)
				$this->success('保存成功');
			else
				$this->error('保存失败');
		}else{
			$this->error('非法操作');
		}
	}

	public function save(){
		$fields = $this->_post('fields', 'htmlspecialchars_deep');
		if(IS_POST and isset($fields)){
			$result = D('Table_field')->saveArray($fields, $this->_post('event_id'));
			if ($result)
				$this->success('保存成功');
			else
				$this->error('保存失败');
		}else{
			$this->error('非法操作');
		}
	}

	public function deleteWholeTable(){
		$event_id = $this->_get('e');
		if ($this->_checkEventBelong($event_id))
			$this->error('非法操作');
		$result = M('Table_field')->where("event_id = '$event_id'")->delete();
		if ($result)
			$this->success('记录已删除');
		else
			$this->error('操作失败');
	}

	public function deleteOneField(){
		$field_id = $this->_get('fid');
		$event_id = $this->_get('eid');
		if ($this->_checkEventBelong($event_id))
			$this->error('非法操作');
		$result = M('Table_field')->delete($field_id);
		if ($result)
			$this->success('记录已删除');
		else
			$this->error('操作失败');
	}
}