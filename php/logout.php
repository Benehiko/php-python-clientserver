<?php
	session_start();
	if ((isset($_COOKIE['edu_LOGIN'])) && (!empty($_COOKIE['edu_LOGIN'])) || ($_SESSION['loggedin'] == true)){
		setcookie('edu_LOGIN',"",time()-3600);
		setcookie("edu_stats","",time()-3600);
		unset($_SESSION['loggedin']);
		
		echo "Logged out!";
	}else{
		echo "Nothing happened!";
	}
?>
