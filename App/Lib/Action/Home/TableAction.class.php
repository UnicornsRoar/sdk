<?php
class TableAction extends Action{
	public function test(){
		$this->display('test');
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
		$where = array('account_id'=>session('account_id'), 'event_id'=>$event_id);
		if (M('Events')->where($where)->count() < 1)
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
		$where = array('account_id'=>session('account_id'), 'event_id'=>$event_id);
		if (M('Events')->where($where)->count() < 1)
			$this->error('非法操作');
		$result = M('Table_field')->delete($field_id);
		if ($result)
			$this->success('记录已删除');
		else
			$this->error('操作失败');
	}
}