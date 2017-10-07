<?php
    ini_set('display_errors', 'On');
	require('dbhandler.php');
	require('password.php');

    /*This file contains code for the use of User login.
    Created by: Alano Terblanche
    Date: 2017-06-30
    Updated: 2017-08-12
    */

    //This file expects the username and password to be posted to it and checks for null or empty values
      if (isset($_POST['username']) && isset($_POST['password'])) {
          //dbhandler is created as an object so that the registerUser function can be used.
          $dbhandler = new dbhandler();

          if (($msg = $dbhandler->registerUser($_POST['username'], $_POST['password'])))
              //boolean - true or false - gets parsed back to the client posting to register.php
              echo $msg;
          else echo $msg;
      }else{echo "Nothing entered!";}

?>
