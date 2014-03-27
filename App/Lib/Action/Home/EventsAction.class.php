<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class EventsAction extends ComEventAction{

	public function __construct(){
		parent::__construct();
	}
	function index(){
        $this->newest();
	}
	function Activity(){
		 if (empty($_SESSION['account_id']))
            $this->error('请先登录');
		$css = array(
                        '__CSS__/page-1.css',
                        '__JS__/jquery-ui-1.8.17.custom/themes/base/jquery.ui.all.css',
                        '__JS__/jquery.imgareaselect-0.9.8/css/imgareaselect-default.css',
						'__JS__/artDialog5.0/skins/default.css',
                    );
        $this->assign('css', $css);

        // 页面所包含的js文件路径
       $js = array(
                        '__JS__/jquery-ui-1.8.17.custom/external/jquery.bgiframe-2.1.2.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.core.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.widget.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.datepicker.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.datepicker-zh-CN.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.mouse.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.draggable.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.position.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.resizable.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.dialog.js',
                        '__JS__/valums-file-uploader/fileuploader.js',
                        '__JS__/jquery.imgareaselect-0.9.8/jquery.imgareaselect.min.js',
                        '__JS__/AjaxFileUploaderV2.1/ajaxfileupload.js',
						'__JS__/ZeroClipBoard/ZeroClipboard.js',
						'__JS__/artDialog5.0/artDialog.min.js'
        ); 
        $this->assign('js', $js);
		$nowTime=time();
		$start_hour_min=createTimeSelection('start_hours','start_minutes',$nowTime);
		$end_hour_min=createTimeSelection('end_hours','end_minutes',($nowTime+60*30));
		$this->assign('start_hour_min',$start_hour_min);
		$this->assign('end_hour_min',$end_hour_min);
		$this->display('activity');
	}

/*
 *编辑活动
 */
	function editActivity(){
		 if (empty($_SESSION['account_id']))
            $this->error('请先登录');
		$event_id = (int) $_GET['event_id'];
		$css = array(
                        '__CSS__/page-1.css',
                        '__JS__/jquery-ui-1.8.17.custom/themes/base/jquery.ui.all.css',
                        '__JS__/jquery.imgareaselect-0.9.8/css/imgareaselect-default.css',
						'__JS__/artDialog5.0/skins/default.css',
                    );
        $this->assign('css', $css);

        // 页面所包含的js文件路径
       $js = array(
                        '__JS__/jquery-ui-1.8.17.custom/external/jquery.bgiframe-2.1.2.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.core.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.widget.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.datepicker.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.datepicker-zh-CN.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.mouse.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.draggable.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.position.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.resizable.js',
                        '__JS__/jquery-ui-1.8.17.custom/ui/jquery.ui.dialog.js',
                        '__JS__/valums-file-uploader/fileuploader.js',
                        '__JS__/jquery.imgareaselect-0.9.8/jquery.imgareaselect.min.js',
                        '__JS__/AjaxFileUploaderV2.1/ajaxfileupload.js',
						'__JS__/ZeroClipBoard/ZeroClipboard.js',
						'__JS__/artDialog5.0/artDialog.min.js'
        );
        $this->assign('js', $js);
        $etime=time();
		$model = D('Events');
		$event = $model->getEventDetails($event_id);
		$start_hour_min=createTimeSelection('start_hours','start_minutes',$event['start_time'],($nowTime));
		$end_hour_min=createTimeSelection('end_hours','end_minutes',$event['end_time'],($nowTime+60*30));
		$this->assign('start_hour_min',$start_hour_min);
		$this->assign('end_hour_min',$end_hour_min);
		if ($event) {
			 $this->assign('event', $event);
		 }
		$this->display();
	}

/*
 * 删除活动
 */
	function delActivity(){
		 if (empty($_SESSION['account_id']))
            $this->error('请先登录');
		if (isset($_GET['event_id'])) {
            $event_id = (int) $_GET['event_id'];

            $Event = D('Events');
            // 检查活动是否本人发布
            $Event->find($event_id);
            // if ($Event->account_id != $_SESSION['account_id']) {
            //     $this->error('非法操作！');
            //     exit;
            // }
            if ($Event->delEvent($event_id)) {
                $this->redirect('manage');
            } else {
                $this->redirect('manage');
            }
        }
	}

/*
 * 管理活动
 */
	function manage(){
		if(!session('?account_id') OR (session("verified")!=1)){
			$jumpUrl = 'Index/index';
			$this->redirect($jumpUrl);
		}
		$model = D('Events');
		$data= array(
			'event_id','event_name','create_time'
		);
		$order=array('create_time' => 'DESC');
		$event=$model->getItems(session('account_id'),$order,$data);
		$this->assign('event',$event);
		$this->display();
	}

/*
 * 发布新活动
 */
	function postActivity(){
		 if (empty($_SESSION['account_id']))
            $this->error('请先登录');
		$loc="新活动发布";
		$this->assign('loc', $loc);
		if(isset($_POST['event_name'])){
			if((empty($_POST['event_name']))||(empty($_POST['start_time']))||(empty($_POST['event_locale']))||(empty($_POST['event_host']))){
				$this->error("请填写完整信息");
			}else{
				$event['account_id']=$_SESSION['account_id'];
                $event['is_posted']=1;
                $event['create_time']=time();
				$event['event_name']=$_POST['event_name'];
				$hour_min=$_POST['start_hours'].":".$_POST['start_minutes'];
				$event['start_time']=$this->makeTime($_POST['start_time'],$hour_min);
				$event['event_locale']=$_POST['event_locale'];
				$event['event_host']=$_POST['event_host'];
				$event['event_details'] = nl2br(str_replace(' ', '&nbsp;', $_POST['event_details']));
				if($end_time = $this->makeTime($_POST['end_time'],$_POST['end_hours'].":".$_POST['end_minutes'])) {
					$event['end_time'] = $end_time;	
				}
				$event['poster_src']=$_POST['poster_src'];
				if ($event['poster_src'] == '') {
					$event['poster_src'] = C('IMG') . '/thumbnail/thumbnailPoster.png';
				}
				if (isset($_POST['edit']) == 1) {
					$event['event_id']=$_POST['event_id'];
					$this->update($event);
				}else{
					$this->insert($event);
				}
			}
		}
	}

	/*
	 * 上传图片
	 */

	function uploadPoster(){
        if(empty($_FILES)){
			echo $result['error']="必须选择上传文件";
        }else{
        	$dir=date('Ym',time());
            $file=$this->upfile($state);
			$result['img_src']="/App/Public/Uploads/".$dir."/m_".$file['img'][0]['savename'];
			$result['error']=$file['error'];
			echo '{';
			echo		"error: '" . $result['error'] . "',\n";
			echo		"img_src: '" . $result['img_src'] . "',\n";
			echo '}';
        }
		
    }

	public function upfile($state){
        //文件上传类的调用
        import('@.ORG.UploadFile');
        $upload=new UploadFile();
        $upload->maxSize='100000000';
        //$upload->savePath='Public/Image/';
        $upload->saveRule=uniqid;
        $upload->uploadReplace=true;
        if($state==0){
        	$dir=date('Ym',time());
             $upload->allowExts=array('jpg','jpeg','png','gif');
            $upload->savePath='./App/Public/Uploads/'.$dir.'/';
            $upload->allowTypes=array('image/png','image/jpg','image/jpeg','image/gif',"image/docx");
            $upload->thumb=true;//开启缩略图
            $upload->thumbMaxWidth='300,500';
            $upload->thumbMaxHeight='200,400';
            $upload->thumbPrefix='m_';//缩略图文件前缀
            $upload->thumbRemoveOrigin=1;//如果生成缩略图，是否删除原图
        }else{
            $upload->allowExts=array('jpg','jpeg','png','gif','doc','docx','rar','zip','txt');
            $upload->savePath='./App/Public/Uploads/';
        }
        if($upload->upload()){
            $info=$upload->getUploadFileInfo();
            $info['img']=$info;
        }else{
            $info['error']=$upload->getErrorMsg();//专门用来获取上传的错误信息的
        }
		return $info;
    }

	/*
	 * 删除海报
	 */
	function deletePoster() {
		 if (empty($_SESSION['account_id']))
            $this->error('请先登录');
        if (isset($_POST['submitted'])) {
            // 如果存在$_POST['event_id'], 说明处于编辑状态
            // 清理数据库中对应活动记录的海报字段
            if (isset($_POST['event_id'])) {

                $event_id = (int) $_POST['event_id'];
                $Event = D('Events');
                $Event->find($event_id);

                // 判断该海报是不是属于本人
                if ($Event->account_id != $_SESSION['account_id']) {
                    $jumpUrl = U('Index/index');
                    $this->assign('jumpUrl', $jumpUrl);
                    $this->error('非法操作！');
                    exit;
                }

                // 海报地址置空
                $Event->poster_src = '/APP/Public/Images/thumbnail/thumbnailPoster.png';
                if (!$Event->save()) {
                    $this->ajaxReturn('', '删除海报失败', 0);
                }
            }
			$deletePoster=C('DOCUMENT_ROOT').$_POST['src'];
			if (file_exists($deletePoster)) {
				if(!unlink($deletePoster)){
					$this->ajaxReturn('', '删除海报失败', 0);
				}
			}else{
				$this->ajaxReturn('', '删除海报失败', 0);
			}
        }
    }
/*
 * 上传视频
 */
	function video(){
		if (empty($_SESSION['account_id']))
			$this->error('请先登录');
		$event_id = (int)$this->_get('event_id');
		$Videos = D('Videos');
		$video=$Videos->getEventVideo($event_id);
		$this->assign('video',$video);
		$this->assign('event_id', $event_id);
		$this->display();
	}
/*
 * 获取视频信息
 */
   public  function getVideo() {
        import('@.ORG.VideoUrlParser');
        $vurl = $this->_post('vurl');
        $vname = $this->_post('vname');
        $event_id=$this->_post('event_id');
        $result['data']= VideoUrlParser::parse($vurl);
        if(!$result['data']) {
            $result['status'] = 0;
            $result['data']['img'] = C('IMG') . '/thumbnail/thumbnailVideo.png';
        } else {
            $result['status'] = 1;
        }
        $Videos=D('Videos');
        $result['data']['event_id']=$event_id;
        $result['data']['vname']=$vname;
        $result['data']['vurl']=$vurl;
        
       if($Videos1 = $Videos->create()){
       		$video_id = $this->_post('video_id');
        	 if($video_id!=''){
        		 $result['data']['video_id']=$this->_post('video_id');
        		 $Videos->saveVideo($result['data']);
        		 $result['method']='save';
        	}else{
        		$result['data']['video_id']= $Videos->addVideos($result['data']);
        		$result['method']='add';
        	}
        }
        	echo json_encode($result);
    }
    /*
     * 删除视频
     */
    function delVideo(){
    	if (empty($_SESSION['account_id']))
    		$this->error('请先登录');
    	if($video_id=$this->_get('v')){
    		$Videos=D('Videos');
    		$event=$Videos->find($video_id);
    		$event_id = $event['event_id'];
    		if ($Videos->delVideo($video_id)) {
    			$this->redirect('/Events/video/event_id/'.$event_id);
    		} else {
    			$this->redirect('/Events/video/event_id/'.$event_id);
    		}
    	}
    }
    /*
     * 更新视频信息
	*/
    function selVideo(){
    	if (empty($_SESSION['account_id']))
    		$this->error('请先登录');
    	if($this->_post('submitted')){
    			$video_id=$this->_post('video_id');
    			$model = D('Videos');
    			$video=$model->selVideo($video_id);
    			if($video){
    				$video['status']=1;	
    			}else{
    				$video['status']=0;
    			}
    			echo  json_encode($video);
    	}
    }
    
	/*
	 * 即将开始活动
	 */
	public function instant(){
		$Model    = D('Events');
		import('@.ORG.Page');
		$page_name='即将开始';
		$this->assign('page_name',$page_name);
		$count= $Model ->getCount();
		$limit=7;
        $Page = new Page($count, $limit);
		$Page->setConfig('header', '');
		$Page->setConfig('pre', '&lt;上一页');
		$Page->setConfig('next', '下一页&gt;');
		$Page -> setConfig('theme', ' <li>%upPage%</li>  %linkPage%</li> %downPage%</li>');
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
		$coming   = $Model->getComing($limit,$nowPage.','.$Page->listRows);
		$page=$Page->show();
		$this->assign('page',$page);
		foreach ($coming as $key => $value) {
			$coming[$key]['start_day'] = $Model->getWeekday($value['start_time']);
		}
		$this->assign('coming',$coming);
		$this->display();
	}
	/*
	 * 最新发布活动
	 */
	public function newest(){
		$Model    = D('Events');
		import('@.ORG.Page');
		$page_name='最新发布';
		$this->assign('page_name',$page_name);
		$count= $Model ->count();
		$limit=7;
        $Page = new Page($count, $limit);
		$Page->setConfig('header', '');
		$Page->setConfig('pre', '&lt;上一页');
		$Page->setConfig('next', '下一页&gt;');
		$Page -> setConfig('theme', ' <li>%upPage%</li>  %linkPage%</li> %downPage%</li>');
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
		$coming   = $Model->getNew($limit,$nowPage.','.$Page->listRows);
		$page=$Page->show();
		$this->assign('page',$page);
		foreach ($coming as $key => $value) {
			$coming[$key]['start_day'] = $Model->getWeekday($value['start_time']);
		}
		$this->assign('coming',$coming);
		$this->display('instant');
	}

	   /**
     * 活动评论页
     */

    public function eventComment(){
        $commentModel = D('Comments');
        $eventModel   = D('Events');
        $eventId      = $this->_get('e');
        if (!$eventModel->checkEventBelong($eventId,$_SESSION['account_id'])){
            $this->error('你没有权限查看该页面');
        }
        $comments     = $commentModel->getEventCommentForHolder($eventId,1,100);
        foreach ($comments as $key => $value) {
            $reply = $commentModel->getOneReplyFromComment($value['comment_id']);
            if ($reply)
                $comments[$key]['reply'] = $reply;
        }
        $event = $eventModel->getEventDetails($eventId);
        $this->assign('event',$event);
        $this->assign('comments',$comments);
        $this->display('eventComment');
    }

    /**
     * 回复留言
     */
    public function reply(){
        if (empty($_SESSION['account_id']))
            $this->error('请先登录,登录后方可评论');
        $Model = D('Comments');
        if($Model->create($_POST,1)){
            $data['comment'] = htmlspecialchars($_POST['comment']);
            $data['account_id'] = $_SESSION['account_id'];
            $data['pre_comment_id'] = $_POST['cid'];
            $Model->insertReply($data);
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->error($Model->getError());
        }
    }

    /**
     * 给活动写评论
     */
    public function writeComment(){
        if (empty($_SESSION['account_id']))
            $this->error('请先登录,登录后方可评论');
        $Model = D('Comments');
        if($Model->create($_POST,1)){
            $data['comment']    = htmlspecialchars($_POST['comment']);
            $data['account_id'] = $_SESSION['account_id'];
            $data['event_id']   = $_POST['eventId'];
            $Model->insertComment($data);
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->error($Model->getError());
        }
    }

    /**
     * 活动详情页面，URL中e为活动ID
     */
    public function activityDetail(){
        $eventId      = $this->_get('e');
        $eventModel   = D('Events');
        $detail       = $eventModel->getEventDetails($eventId);
        // 处理周“几”
        $detail['start_day'] = $eventModel->getWeekday($detail['start_time']);
        if ($detail['end_time'] != 0)
            $detail['end_day'] = $eventModel->getWeekday($detail['end_time']);
        // 取出收藏数
        $collectionModel = D('Collections');
        $detail['loves'] = $collectionModel->getEventCollectCount($eventId);
        if ($collectionModel->isCollected($eventId))
            $this->assign('isCollected',1); //已收藏
        // 取出评论
        if ($detail['has_comments'] == 1){
            $commentModel = D('Comments');
            $comments     = $commentModel->getEventComment($eventId,1,100);
            $this->assign('comments',$comments);
        }
        // 取出视频信息
        if ($detail['has_videos'] == 1){
            $videoModel = D('Videos');
            $videos = $videoModel->getEventVideo($eventId);
            $this->assign('videos',$videos);
        }
        // 取出附件信息
        if ($detail['has_attachments'] == 1){
            $attachModel = D('Attachments');
            $attachments = $attachModel->getEventAttachment($eventId);
            $this->assign('attachments',$attachments);
        }
        $this->assign('detail',$detail);
        $this->display('activity-detail');
    }

    /**
     * 收藏活动
     */
    public function marklike(){
        $data['c_event_id'] = $_POST['eventId'];
        $data['account_id'] = $_SESSION['account_id'];
        $Model  = M('Collections');
        $result = $Model->add($data);
        echo $result ? 1 : 0;
    }


    /**
     * 取消收藏
     */
    public function unlike(){
        $Model  = M('Collections');
        $where  = "c_event_id = '{$_POST['eventId']}' AND account_id = '{$_SESSION['account_id']}'";
        $result = $Model->where($where)->delete();
        echo ($result === FALSE) ? 0 : 1;
    }

    public function attachment(){
        $eventId     = $this->_get('e');
        $eventModel  = D('Events');
        $attachModel = D('Attachments');
        $attach      = $attachModel->getEventAttachment($eventId);
        $event       = $eventModel->getEventDetails($eventId);
        $this->assign('attachments',$attach);
        $this->assign('event',$event);
        $this->display('attachment');
    }

    /**
     * 报名表设置
     */
    public function eventEntry(){
        $event_id   = $this->_get('e');
        $fieldModel = D('Table_field');
        $fields     = $fieldModel->getEventFields($event_id);
        if (count($fields)){
            $this->assign('hasSetTable', 0);
            $this->display();
        }else{
            $this->assign('hasSetTable', 1);
            $this->assign('fields', $fields);
            $this->display();
        }
	}

    /**
     * 查看报名人数信息
     */
    public function entryList(){
        $event_id = $this->_get('e');
        if (!empty($event_id)){
            $fieldModel = D('Table_field');
            // 获取分页
            $pageNum = $this->_get('p', 'htmlspecialchars', 1);
            $count   = $fieldModel->getSignCount($event_id);
            $limit   = 15;
            $pager   = new Page($count, $limit);

            $signedUsers = $fieldModel->getSignUsers($event_id, $pageNum, $limit);
            $fields      = $fieldModel->getEventFields($event_id);
            $table       = $fieldModel->createTable($fields, $signedUsers);

            // 确保fields按照field_id排序
            $sortedFields = array();
            foreach ($fields as $value) {
                $sortedFields[$value['field_id']] = $value['field_name'];
            }

            $this->assign('fields', $sortedFields);
            $this->assign('tabel', $table);
            $this->assign('page', $pager->show());
    		$this->display();
        }else{
            $this->error('非法操作！');
        }
	}
}
?>
