<?php
class ComEventAction extends Action{
	public function _construct(){
		parent::_construct();
	}
	public function index() {}

	/*
	 * 基础函数调用：新增操作
	 */
	function insert($data=array()){
		$model=D($this->getActionName());
		if(false===$model->create()){
			$this->error($model->getDbError());
		}
		$result=$model->add($data);
		if($result){
			$this->redirect('manage');
		}else{
			$this->error("新增失败");
		}
	}

	/*
	 * 基础函数调用：删除操作
	 */
	function del(){
		$model=D($this->getActionName());
		if(!empty($model)){
			$pk=$model->getPk();
			$id=$_REQUEST[$pk];
			if(isset($id)){
				$condition=array($pk=>array('in',explode(',',$id)));
				if(false!==$model->where($condition)->delete()){
					$this->success("删除成功");
				}else{
					$this->error("删除失败");
				}
			}
		}else{
			$this->error("非法操作");
		}
	}

	/*
	 * 基础函数调用：更新操作
	 */
	function update($data=array()){
		$model=D($this->getActionName());
		if(false===$model->create()){
			$this->error($model->getError());
		}
		if(false!==$model->save($data)){
			$this->redirect('manage');
		}else{
			$this->error(L('更新失败'));
		}
	}

	/*
	 * 基础函数调用：选择操作
	 */
	function select(){
		$model=D($this->getActionName());
	}


	/*
	 * 验证码
	 */
	function verify() {
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}

	/*
	 * 对时间进行处理
	 */
	function makeTime($day = '1970-00-00', $hour_minute = '00:00') {
        // 如果开始日期不为空
        if ($day !== '') {
            // 组合活动开始时间
            $time = $day;

            // 如果开始时间不为空
            if ($hour_minute !== '') {
                $time = $time . ' ' . $hour_minute . ':00';
            }

            // 转换成时间戳
            $time = strtotime($time);
            // 将活动开始时间赋值给即将插入数据库的数组$event
            if ($time !== FALSE && $time !== -1) {
                return $time;
            }
        }
        return false;
    }
	
	/*
	 * 空操作
	 */
	function _empty(){
		$this->error("该页面不存在");
	}


}
?>
