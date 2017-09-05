<?php
ini_set('display_errors', 'On');
session_start();

		if ((isset($_POST['sessionid'])) && (!empty($_POST['sessionid']))){
			if ($_POST['sessionid'] == $_SESSION['sessionid'])
				echo "true";
			else echo "false";
		}



?>
