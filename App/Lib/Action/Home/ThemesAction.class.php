<?php
class ThemesAction extends Action{
	public function theme(){
		$themeId = $this->_get('id');
		$themeModel = M('Themes');
		$themeInfo = $themeModel->find($themeId);
		$tagsModel = M('Tags');
		$nowPage = isset($_GET['p'])?$_GET['p']:1;// 页码
		$limit = 7;
		$events = $tagsModel->field('event_id')->where("theme_id = $themeId")->order('event_id desc')->page($nowPage,$limit)->select();
		$eventModel = D('Events');

		// 获取主题活动信息
		$eventInfo = array();
		foreach ($events as $key => $value) {
			$eventInfo[] = $eventModel->getEventDetails($value['event_id']);
		}
		// 获取每个活动的周“几”
		$eventInfo = $eventModel->getWeekdayInBatch($eventInfo);
		// 分页
		import('@.ORG.Page');
		$limit = 7;
		$count = $tagsModel->where("theme_id = $themeId")->count();
		$Page = new Page($count, $limit);
		$Page->setConfig('header', '');
		$Page->setConfig('pre', '&lt;上一页');
		$Page->setConfig('next', '下一页&gt;');
		$Page->setConfig('theme', ' <li>%upPage%</li>  %linkPage%</li> %downPage%</li>');
		$page=$Page->show();
		$this->assign('themeInfo',$themeInfo);
		$this->assign('eventInfo',$eventInfo);
		$this->display();
	}
}