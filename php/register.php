<?php
    ini_set('display_errors', 'On');
	require('dbhandler.php');
	require('password.php');

  if (isset($_POST['username']) && isset($_POST['password'])) {


      //$db = new DBConnect;
      //$mysqli = $db->connect();
      //$emailhandler = new emailhandler();
      $dbhandler = new dbhandler();

      echo $msg = $dbhandler->registerUser($_POST['username'], $_POST['password']);
  }else{echo "Nothing entered!";}

?>
