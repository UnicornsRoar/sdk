<?php
	session_start();

	if(isset($_SESSION['token'])){
		unset($_SESSION['token']);
		session_destroy();
		echo '退出成功';
	}
	
	//header('Location:weibolist.php');
?>