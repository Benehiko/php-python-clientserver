<?php
        session_start();
	
		if ((isset($_COOKIE['edu_LOGIN'])) && (!empty($_COOKIE['edu_LOGIN']))){
			echo $_COOKIE['edu_LOGIN'];
		}else if ($_SESSION['loggedin']){
			echo $_SESSION['username'];
		}else{
            echo "LoggedOut";
        }


?>
