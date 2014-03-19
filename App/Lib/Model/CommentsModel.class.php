<?php
/**
 * 评论处理模型
 * @author Don
 */
class CommentsModel extends Model{

	/**
	 * 自动验证
	 */
	protected $_validate = array(
		array('comment','require','不能发表空评论哦。')
	);

	/**
	 * 自动完成
	 */
	protected $_auto = array(
		array('comment', 'htmlspecialchars', 1,'function'),
		array('comment', 'remove_xss', 1, 'callback'),
		array('comment', 'h', 1, 'callback'),
        array('comment', 'parseTags', 3, 'callback')
	);

	/**
	 * 获取评论者的姓名
	 * @param  int $accountId用户ID
	 * @return string
	 */
	public function getCommentUserName($accountId){
		$user = $this->field('user_name')->table('sdk_accounts')->where("account_id = $accountId")->find();
		return $user['user_name'];
	}

	/**
	 * 获取活动名字
	 * @param  int $eventId 
	 * @return string
	 */
	public function getEventName($eventId){
		$event = $this->field('event_name')->table('sdk_events')->where("event_id = $eventId")->find();
		return $event['event_name'];
	}

	/**
	 * 获取一级评论
	 * @param  int  $eventId 活动ID
	 * @param  integer $page 页数，默认为1
	 * @param  integer $limit 每页展示评论数，默认为10
	 * @return array|void    返回的评论数组
	 */
	public function getFirstComment($eventId,$page=1,$limit=10){
		$field = 'comment_id,comments.account_id,comment,post_time,user_name';
		$table = array('sdk_comments'=>'comments','sdk_accounts'=>'accounts');
		$where = "comments.account_id = accounts.account_id AND comments.event_id='$eventId' AND comments.source_comment_id=0";
		return $this->field($field)->Table($table)->where($where)->page("$page,$limit")->select();
	}

	/**
	 * 获取二级评论
	 * @param  integer $firstId 对应一级评论的id
	 * @param  integer $page    页数，默认为1
	 * @param  integer $limit   每页显示评论数，默认为5
	 * @return array|null|false 二级评论数组
	 */
	public function getSecondComment($firstId,$page=1,$limit=100){
		$field = 'comment_id, comment, post_time,pre_comment_id,
				  accounts.user_name AS this_acc_name, accounts.account_id AS this_acc_id';
		$table = 'sdk_comments comments,sdk_accounts accounts';
		$where = "comments.source_comment_id = '$firstId'
				  AND comments.account_id=accounts.account_id";
		//提取所有二级评论
		$result= $this->field($field)->Table($table)->where($where)->page("$page,$limit")->order('post_time asc')->select();
		if ($result){
			// 查出本批次二级评论中的第一个评论的作者的信息
			$tmp_comment = array_shift($result);
			$tmp_content = $this->field('user_name,sdk_comments.account_id')->table('sdk_accounts,sdk_comments')
			->where("sdk_accounts.account_id=sdk_comments.account_id AND comment_id='{$tmp_comment['pre_comment_id']}'")->find();
			$tmp_comment['pre_name']   = $tmp_content['user_name'];
			$tmp_comment['pre_acc_id'] = $tmp_content['account_id'];
			// 查处后放进数组sort中
			$sort  = array($tmp_comment['comment_id'] => $tmp_comment);
			// 迭代处理剩余的二级评论,吧评论的comment_id作为sort的下标，便于随机查找，加入回复对象的名字和ID，有点麻烦。。。
			if ($result){
				foreach ($result as $key) {
					$sort[$key['comment_id']] = $key;
					$sort[$key['comment_id']]['pre_acc_id'] = $sort[$key['pre_comment_id']]['this_acc_id'];
					$sort[$key['comment_id']]['pre_name']   = $sort[$key['pre_comment_id']]['this_acc_name'];
				}
			}
			return $sort;
		}
		return $result;
	}

	/**
	 * 获取活动评论
	 * @param  integer $eventId 活动ID
	 * @param  integer $page    页数，默认为1
	 * @param  integer $limit   每页评论数，默认为10
	 * @return array|null|false 评论内容数组
	 */
	public function getEventComment($eventId,$page=1,$limit=100){
		//获取一级评论
		$first = $this->getFirstComment($eventId,$page,$limit);
		if ($first) {
			//获取二级评论并组合进一级评论
			foreach ($first as $key => $value) {
				$second = $this->getSecondComment($value['comment_id']);
				if ($second) {
					$first[$key]['second'] = $second;
				}
			}
		}
		return $first;
	}

	/**
	 * 获取用户收到的评论
	 * @param  integer $accountId 用户ID
	 * @param  integer $page      页码
	 * @param  integer $limit     每页评论数
	 * @return array|null         评论信息
	 */
	public function getAccountReceiveComment($accountId,$page=1,$limit=10){
		// 从用户发布的活动中收到的所有评论
		$eventField = 'comment_id, comment, comment.event_id, post_time, comment.account_id AS account_id';
		$eventTable = 'sdk_comments AS comment,sdk_events AS event';
		$eventWhere = "event.account_id = '$accountId' AND comment.event_id = event.event_id AND comment.account_id!='$accountId'";
		$eventSql   = $this->field($eventField)->table($eventTable)->where($eventWhere)->buildSql();

		// 从别人回复该用户的评论中提取
		$commentField = 'reply_comment.comment_id,
						 reply_comment.comment,
						 reply_comment.event_id,
						 reply_comment.post_time,
						 reply_comment.account_id';
		$commentTable = 'sdk_comments AS reply_comment,sdk_comments AS your_comment';
		$commentWhere = "your_comment.account_id = '$accountId'
						 AND reply_comment.account_id != '$accountId'
						 AND (reply_comment.source_comment_id = your_comment.comment_id
						 	  OR reply_comment.pre_comment_id = your_comment.comment_id)";
		$commentSql   = $this->field($commentField)->table($commentTable)->where($commentWhere)->buildSql();

		// 组合查询
		$limit    = 'LIMIT '.($page-1)*$limit.",$limit";
		$finalSql = $eventSql.' UNION '.$commentSql.'ORDER BY post_time '.$limit;
		$result   = $this->query($finalSql);

		// 检索出数据后，再逐个查处评论者的名字以及活动的名字
		foreach ($result as $key => $value) {
			$result[$key]['user_name']  = $this->getCommentUserName($value['account_id']);
			$result[$key]['event_name'] = $this->getEventName($value['event_id']);
		}
		return $result;
	}

	/**
	 * 插入评论
	 * @param  array $data  评论数据
	 * @return int 
	 */
	public function insertComment($data){
		$data['post_time']  = time();
		$data['account_id'] = $_SESSION['account_id'];
		$where = "event_id = '{$data['event_id']}'";
		$event = M('Events');
		$event->where($where)->setField('has_comments',1);
		return $this->table('sdk_comments')->add($data);
	}

	/**
	 * 删除评论，同时把相关回复删除
	 * @param  int $commentId 
	 * @return int|false   删除的评论条数或删除失败
	 */
	public function deleteComment($commentId){
		$condition['comment_id']        = $commentId;
		$condition['pre_comment_id']    = $commentId;
		$condition['source_comment_id'] = $commentId;
		$condition['_logic']            = 'OR';
		return ($this->where($condition)->delete($commentId));
	}

	/**
	 * 回复留言,自动补充时间和pre_comment和source_comment等
	 * @param  array $data 
	 * @return int|false   插入成功或失败
	 */
	public function insertReply($data){
		$data['post_time'] = time();
		$pre_comment = $this->field('event_id,source_comment_id')->find($data['pre_comment_id']);
		if ($pre_comment['source_comment_id'] == 0)
			$pre_comment['source_comment_id'] = $data['pre_comment_id'];
		$data = array_merge($data,$pre_comment);
		return $this->add($data);
	}

	/**
	 * 根据一个评论的ID获取回复，回复者为登录的用户
	 * @param  int $commentId
	 * @return array|null|flase 
	 */
	public function getOneReplyFromComment($commentId){
		return $this->where("pre_comment_id = '$commentId' AND account_id = '{$_SESSION['account_id']}'")->find();
	}

	/**
	 *  取出针对某个活动除了举办者自己的发表评论
	 * @param  int $eventId 活动ID
	 * @param  int $page    页数
	 * @param  int $limit   每页容量
	 * @return array|null|false 二维数组
	 */
	public function getEventCommentForHolder($eventId,$page=1,$limit=10){
		$field  = 'comment_id, comment, account_id, event_id, post_time';
		$where  = "event_id = '$eventId' AND account_id != '{$_SESSION['account_id']}'";
		$result = $this->field($field)->where($where)->page("$page,$limit")->order('post_time desc')->select();
		// 查出用户名和活动名
		foreach ($result as $key => $value) {
			$result[$key]['user_name']  = $this->getCommentUserName($value['account_id']);
			$result[$key]['event_name'] = $this->getEventName($value['event_id']);
		}
		return $result;
	}

	/**
	 * 管理页面的评论管理信息
	 * @param  int     $page  页数
	 * @param  integer $limit 每页容量
	 * @return array|null|false 二维数组
	 */
	public function getAdminComments($page, $limit=15){
		$field  = 'comment_id, comment, account_id, event_id, post_time';
		$result = $this->field($field)->page("$page,$limit")->order('comment_id desc')->select();
		// 查出用户名和活动名
		foreach ($result as $key => $value) {
			$result[$key]['user_name']  = $this->getCommentUserName($value['account_id']);
			$result[$key]['event_name'] = $this->getEventName($value['event_id']);
		}
		return $result;
	}

	// XSS安全过滤
	public function remove_xss($val) {
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=@avascript:alert('XSS')>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
	      // ;? matches the ;, which is optional
	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

	      // @ @ search for the hex values
	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	      // @ @ 0{0,7} matches '0' zero to seven times
	      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }

	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);

	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
	      $val_before = $val;
	      for ($i = 0; $i < sizeof($ra); $i++) {
	         $pattern = '/';
	         for ($j = 0; $j < strlen($ra[$i]); $j++) {
	            if ($j > 0) {
	               $pattern .= '(';
	               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
	               $pattern .= '|';
	               $pattern .= '|(&#0{0,8}([9|10|13]);)';
	               $pattern .= ')*';
	            }
	            $pattern .= $ra[$i][$j];
	         }
	         $pattern .= '/i';
	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	         if ($val_before == $val) {
	            // no replacements were made, so exit the loop
	            $found = false;
	         }
	      }
	   }
	   return $val;
	}
	//输出安全的html
	public function h($text){
		$text	=	trim($text);
		//完全过滤注释
		$text	=	preg_replace('/<!--?.*-->/','',$text);
		//完全过滤动态代码
		$text	=	preg_replace('/<\?|\?'.'>/','',$text);
		//完全过滤js
		$text	=	preg_replace('/<script?.*\/script>/','',$text);

		$text	=	str_replace('[','&#091;',$text);
		$text	=	str_replace(']','&#093;',$text);
		$text	=	str_replace('|','&#124;',$text);
		//过滤换行符
		$text	=	preg_replace('/\r?\n/','',$text);
		//br
		$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
		$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
		//过滤危险的属性，如：过滤on事件lang js
		while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1],$text);
		}
		while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1].$mat[3],$text);
		}
		/*if(empty($tags)) {
			$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
		}
		//允许的HTML标签
		$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);*/
		//过滤多余html
		$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
		//过滤合法的html标签
		while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
			$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
		}
		//转换引号
		while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
		}
		//过滤错误的单个引号
		while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
			$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
		}
		//转换其它所有不合法的 < >
		$text	=	str_replace('<','&lt;',$text);
		$text	=	str_replace('>','&gt;',$text);
		$text	=	str_replace('"','&quot;',$text);
		 //反转换
		$text	=	str_replace('[','<',$text);
		$text	=	str_replace(']','>',$text);
		$text	=	str_replace('|','"',$text);
		//过滤多余空格
		$text	=	str_replace('  ',' ',$text);
		return $text;
	}

	/**
     * 标签解析
     *
     * 将<quote></quote>解析为<p class="quote"></p>
     * 将<at></at>解析为<span></span>
     *
     * @para string $data 要解析的文本
     */
    public function parseTags($data) {
        $data = preg_replace('/[\s]+/', ' ', $data);
        // 不贪婪匹配
        $pattern = '/((<quote>)(<at>)(.*)(<\/at>)(.*)(<\/quote>))((?!<quote>).*)/';
        $replacement = '<p class="quote"><span>@$4</span>$6</p><p>$8</p>';
        
        if (preg_match($pattern, $data)) {
            $data = preg_replace($pattern, $replacement, $data, 1);
            // 贪婪匹配, 提取从引用到结尾的内容
            $pattern = '/<p class="quote">.*<\/p>/';
            preg_match($pattern, $data, $matches);
            $data = $matches[0];
        } else {
            $data = '<p>' . $data . '</p>';
        }
        
        return trim($data);
    }

}

