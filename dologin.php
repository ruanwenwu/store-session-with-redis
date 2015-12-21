<?php
require "SessionManager.class.php";
require "cpdo.class.php";
$pdo = new Cpdo;

//通过sql语句获得值
//$sql = "select * from `dede_feedback` where `id` < 20";
//$res = $pdo->getValueBySelfCreateSql($sql);

if(false){
	echo 'haha';
	header("location:./index.php");
	die;
}else{
	$_SESSION['islogin'] = true;	
	$_SESSION['username'] = "ruanwenwu";
	$_SESSION['uid'] = 1;
	
	//获得上一条用户的sessionid,并删除对应的session
	$oldSql = "select * from session_record where uid = 1 order by lastlogin desc";
	$res = $pdo->getValueBySelfCreateSql($oldSql);
	if($res){
		$old_session_id = $res[0]['session_id']; 
		$sessionManager->destroy($old_session_id);
	}

	//将用户的这条session写入数据库
	$sql = "insert into session_record (session_id,lastlogin,uid,ip) values ('".session_id()."','".time()."','1','{$_SERVER['REMOTE_ADDR']}')";
	$pdo->exec($sql);
	
	//成功写入新的登陆记录
	header("location:./index.php");
	
	
}
?>