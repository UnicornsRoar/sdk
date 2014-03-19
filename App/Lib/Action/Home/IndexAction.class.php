<?php
class IndexAction extends ComEventAction {
	public function index(){
		$js=array(
			'__JS__/validation.js',
		);
		$this->assign('js',$js);
		$Model    = D('Events');
		$hotModel = D('Hots');
		$hot      = $hotModel->getHot();
		foreach ($hot as $key => $value) {
			$hot[$key]['start_day'] = $Model->getWeekday($value['start_time']);
		}
		$coming   = $Model->getComing();
		foreach ($coming as $key => $value) {
			$coming[$key]['start_day'] = $Model->getWeekday($value['start_time']);
		}
		$new      = $Model->getNew();
		foreach ($new as $key => $value) {
			$new[$key]['start_day'] = $Model->getWeekday($value['start_time']);
		}
    	$this->assign('hot',$hot);
    	$this->assign('coming',$coming);
    	$this->assign('new',$new);
		$this->display();
    }
}