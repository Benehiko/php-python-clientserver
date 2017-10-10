<?php
ini_set('display_errors', 'On');
require_once("dbhandler.php");

		if ((isset($_POST['id'])) && (!empty($_POST['id'])) && isset($_POST['token']) && (!empty($_POST['token']))){
			$dbhandler = new dbhandler();
			$msg = $dbhandler->verifyToken($_POST['id'], $_POST['token']);
			echo $msg;
		}else echo "Empty Data";



?>
