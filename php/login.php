<?php
	ini_set('display_errors', 'On');
	require_once('dbhandler.php');

	if (isset($_POST['username']) && isset($_POST['password']) && (!empty($_POST['username'])) && (!empty($_POST['password']))){
			$dbhandler = new dbhandler();
			$msg = $dbhandler->login($_POST['username'],$_POST['password']);
            if (is_array($msg)){
                echo json_encode($msg);
            }else echo $msg;

		}else echo "Empty values given";



?>
