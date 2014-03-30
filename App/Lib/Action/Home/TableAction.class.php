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
    		if (M('Field_record')->addAll($data))
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

	/**
	 * 编辑已经报名的信息
	 */
	public function editSubmit(){
		$event_id = $this->_get('e');
		$fieldModel = D('Table_field');
		if (IS_POST){
			// 判断所有记录是不是属于登陆的用户
			foreach ($_POST as $key => $value) {
				$recordIds[] = $key;
			}
			if (session('account_id') != $fieldModel->confirmOneUser($recordIds))
				$this->error('数据出错');

			$affected_rows = 0;
			$recordModel = M('Field_record');
			// 逐项保存
			foreach ($_POST as $key => $value) {
				if (!is_int($key))
					continue;
				$data = array(
						'record_id' => $key,
						'content' => $this->_post($key)
					);
				if ($recordModel->save($data))
					$affected_rows++;
			}
			if ($affected_rows)
				$this->success('成功保存');
			else
				$this->error('保存失败');
		}else{
			$info = $fieldModel->getOneRecord($event_id, session('account_id'));
			$this->assign('info', $info);
			$this->display();
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
		if (!$this->_checkEventBelong($event_id))
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
		if (!$this->_checkEventBelong($event_id))
			$this->error('非法操作');
		$result = M('Table_field')->delete($field_id);
		if ($result)
			$this->success('记录已删除');
		else
			$this->error('操作失败');
	}

	/**
	 * 组装CSV字符串
	 * @param  array $fields
	 * @param  array $table
	 * @return string
	 */
	private function _getCSV($fields, $table){
		$csv = implode(',', $fields)."\n";
        $csv = iconv('utf-8', 'gb2312', $csv);
        foreach ($table as $row) {
        	$tem = array();
        	foreach ($row as $col) {
        		$tem[] = iconv('utf-8', 'gb2312', $col['content']);
        	}
        	$temStr = implode(',', $tem);
        	$csv .= $temStr."\n";
        }
        return $csv;
	}

	/**
	 * 导出CSV文件
	 * @param  string $filename
	 * @param  string $CSVStr  
	 * @return file
	 */
	private function _exportCSV($filename, $CSVStr){
	    header("Content-type:text/csv"); 
	    header("Content-Disposition:attachment;filename=".$filename); 
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
	    header('Expires:0'); 
	    header('Pragma:public'); 
	    echo $CSVStr;
	}

	public function exportPageCSV(){
		$event_id = $this->_get('e');
		if (!$this->_checkEventBelong($event_id))
			$this->error('无效链接');
		$page = $this->_get('p', 'htmlspecialchars', 1);
		$fieldModel = D('Table_field');
		
		$signedUsers = $fieldModel->getSignUsers($event_id, $page);
		$fields      = $fieldModel->getEventFields($event_id);
		$table       = $fieldModel->createTable($fields, $signedUsers);

		// 取出字段先
		$sortedFields = array();
        foreach ($fields as $value) {
            $sortedFields[$value['field_id']] = $value['field_name'];
        }
        ksort($sortedFields); // 排好序

        // 组装CVS
        $csv = $this->_getCSV($sortedFields, $table);

        // 导出csv文件
        $filename = 'event_'.$event_id.'_page'.$page.'_'.date('Ymd').".csv";
        $this->_exportCSV($filename, $csv);
	}
}