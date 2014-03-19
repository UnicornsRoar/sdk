<?php
class EventsModel extends Model{


    public function addEvent($data = array()) {
		// 替换空格和'/n'
        // 如果海报图为空, 设为默认海报图
		$data['event_details'] = nl2br(str_replace(' ', '&nbsp;', $data['event_details']));
        if ($data['poster_src'] == '') {
            $data['poster_src'] = C('IMG') . '/thumbnail/thumbnailPoster.png';
        }
        $result = $this->add($data);
        return $result;
    }

    public function getEventDetails($event_id) {
		$event = $this->find($event_id);
        if ( $event){
            return $event;
        } else {
            return false;
        }
    }

    function delEvent($event_id) {
        $event_id = (int) $event_id;
       
        if ($this->delete($event_id)) {
            return true;
        } else {
            return false;
        }
    }


   /**
    * 检查活动时候由某用户发布
    * @param  int $eventId
    * @param  int $accountId
    * @return bool
    */
   public function checkEventBelong($eventId,$accountId){
        $result = $this->field('event_id')->where("event_id = '$eventId' AND account_id = '$accountId'")->find();
        return is_array($result) ? true : false;
    }

	




	function getItems($account_id,$order = array('create_time' => 'DESC'),$data) {
       $event = $this->where("account_id=$account_id")
								   ->field($data)
								   ->select();
        return $event;
    }



    /**
     * 转换中文星期
     * @param  int $timestamp
     * @return string 
     */
    public function getWeekday($timestamp){
        $day = date('N',$timestamp);
        switch ($day) {
            case '1': return '一'; break;
            case '2': return '二'; break;
            case '3': return '三'; break;
            case '4': return '四'; break;
            case '5': return '五'; break;
            case '6': return '六'; break;
            default:  return '日'; break;
        }
    }

    /**
     * 批量获取中文星期
     * @param  [type] $eventArray [description]
     * @return [type]             [description]
     */
    public function getWeekdayInBatch($eventArray){
        foreach ($eventArray as $key => $value) {
            $eventArray[$key]['start_day'] = $this->getWeekday($value['start_time']);
        }
        return $eventArray;
    }

    /**
     * 获取即将开始的活动
     * @return array
     */
    public function getComing($limit=5,$pageStart){
        $time = mktime(0,0,0)-2;
		if(isset($pageStart)){
			$result = $this->where("start_time > $time AND is_posted = 1")->order('start_time asc')->page($pageStart)->select();
		}else{
			$result = $this->where("start_time > $time AND is_posted = 1")->order('start_time asc')->limit($limit)->select();
		}

        foreach ($result as $key => $value) {
            $day = $this->getTakePlaceDay($value['start_time']);
            $result[$key]['day'] = $day;
        }
        return $result;
    }

    public function getCount(){
        $time = mktime(0,0,0)-2;
        $result = $this -> where("start_time > $time AND is_posted = 1")->count();
        return $result;
    }

    /**
     * 获取最新发布的活动
     * @return array
     */

    public function getNew($limit=5,$page){
		if(isset($page)){
			$result = $this->order('create_time desc')->where('is_posted = 1')->page($page)->select();
		}else{
			$result = $this->order('create_time desc')->where('is_posted = 1')->limit($limit)->select();
		}

        // foreach ($result as $key => $value) {
        //     $date = $this->getTakePlaceDate($value['start_time']);
        //     $result[$key]['date'] = $date;
        // }
        return $result;
    }

    /**
     * 获取几天后活动开始
     * @param  int $eventTime 活动开始时间戳
     * @return array
     */
    public function getTakePlaceDay($eventTime){
        import('@.ORG.Date');
        $thisDate = date('Y-m-d');
        $eventDate = date('Y-m-d',$eventTime);
        $date = new Date($thisDate);
        $diff = $date->dateDiff($eventDate,'d');
        switch ($diff) {
          case 0 : $day['info'] = '今天'; $day['class']='l-today';break;
          case 1 : $day['info'] = '明天';$day['class']='tomorrow'; break;
          case 2 : $day['info'] = '后天'; $day['class']='threedays';break;
          default: $day['info'] = '3天后'; $day['class']='threedays';break;
        }
        return $day;
    }

    /**
     * 批量获取TakePlaceDay
     * @param  array $eventArray
     * @return array
     */
    public function getTakePlaceDayInBatch($eventArray){
        foreach ($eventArray as $key => $value) {
            $eventArray[$key]['day'] = $this->getTakePlaceDay($value['start_time']);
        }
        return $eventArray;
    }

    /**
     * 供api获取即将到来的活动
     * @param  int $page 页码
     * @param  int $limit
     * @return array|null
     */
    public function apiGetComing($page,$limit){
        $time = mktime(0,0,0)-2;
        $field = 'account_id AS account, event_id, event_name, poster_src, event_host, start_time, end_time, event_locale, event_details';
        $events = $this->field($field)->where("start_time > $time AND is_posted = 1")->order('start_time asc')->page($page.','.$limit)->select();
        return $events;
    }

    public function apiGetNew($page,$limit){
        $field = 'account_id AS account, event_id, event_name, poster_src, event_host, start_time, end_time, event_locale, event_details';
        $events = $this->field($field)->order('create_time desc')->page($page.','.$limit)->select();
        return $events;
    }

    public function adminGetEventDetails($page=1){
        $fields = 'account_id,event_id,event_name,event_host,create_time';
        $events = $this->field($fields)->page($page.',15')->order('event_id DESC')->select();
        $events = D('Accounts')->getUserNameInBatch($events);
        return $events;
    }

    /**
     * 得到页码列表
     * @param int $count 为内容总数，总共有多少项记录
     *  @return String
     */
    public function getPages($count) {
        import('@.ORG.Page');
        $page   = new Page($count, 15);
        return $page->show();
    }
}



?>
