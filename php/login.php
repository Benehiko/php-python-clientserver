<?php
	require_once('dbhandler.php.php');

	if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){

			$dbhandler = new dbhandler();
			$msg = $dbhandler->login($_POST['username'],$_POST['password']);
            if ($msg.is_array()){
                echo json_encode($msg);
            }else echo $msg;

		}else echo "Empty values given";



?>
