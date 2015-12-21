<?php
require "SessionManager.class.php";
if(isset($_SESSION['islogin']) && $_SESSION['islogin']){
?>
	欢迎你：<?php echo $_SESSION['username']; ?>;
<?php
}else{
	header("location:./login.php");
}
?>