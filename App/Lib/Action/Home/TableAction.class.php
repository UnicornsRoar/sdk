<?php
class TableAction extends Action{

	/**
	 * 判断活动是否属于用户
	 * @param  int $event_id
	 * @param  int $account_id
	 * @return bool
	 */
	private function _checkEventBelong($event_id, $account_id=NULL){
		if ($account_id == NULL) $account_id = session('account_id');
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

	public function editEntry(){
		$event_id = $this->_get('e');
		if ($this->_checkEventBelong($event_id)){
			$this->display('editEntry');
		}else{
			$this->error('非法操作！');
		}
	}

    public function submitForm(){
    	$
		$this->display('submitForm');
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