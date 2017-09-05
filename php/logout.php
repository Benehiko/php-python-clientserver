<?php
	session_start();
	if (isset($_POST['sessionid']) && (!empty($_POST['sessionid']))){
		if ($_SESSION['sessionid'] == $_POST['sessionid']){
			session_destroy();
 		}
 		echo "Logged out";
	}else echo "No session data sent";
?>
