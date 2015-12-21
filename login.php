<?php
require "SessionManager.class.php";
if(isset($_SESSION['islogin']) && $_SESSION['islogin']){
	header("location:./index.php");
	die;
}else{
?>
	<form action="./dologin.php" />
		用户名：<input type="text" name="username" />
		密  码：<input type="text" name="pwd" />
		<input type="submit" value="submit" />
	</form>
<?php
}
?>