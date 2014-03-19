<?php
class ApiAction extends Action{

	/**
	 * 供api获取即将到来的活动
	 * 以json格式返回
	 */
	public function comingEvents(){
		// 获取分页信息
		$limit = empty($this->_get('l')) ? $this->_get('l') : 1;
		$page = empty($this->_get('p')) ? $this->_get('p') : 5;

		$Event = D('Events');
		$result = $Event->apiGetComing($page,$limit);
		$Account = D('Accounts');
		foreach ($result as $key => $value) {
			$result[$key]['account'] = $Account->getUserName($value['account']);
		}
		echo json_encode($result);
	}

	/**
	 * 供api获取即将到来的活动
	 * 以json格式返回
	 */
	public function newEvents(){
		// 获取分页信息
		$limit = empty($this->_get('l')) ? $this->_get('l') : 1;
		$page = empty($this->_get('p')) ? $this->_get('p') : 5;

		$Event = D('Events');
		$result = $Event->apiGetNew($page,$limit);
		$Account = D('Accounts');
		foreach ($result as $key => $value) {
			$result[$key]['account'] = $Account->getUserName($value['account']);
		}
		echo json_encode($result);
	}
}