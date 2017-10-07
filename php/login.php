<?php
	//ini_set('display_errors', 'On');
	require_once('dbhandler.php');

	/*This file contains code for the use of User login.
	Created by: Alano Terblanche
	Date: 2017-06-30
	Updated: 2017-08-12
	*/

	//The username and password gets posted to this file and gets checked for empty values.
	if (isset($_POST['username']) && isset($_POST['password']) && (!empty($_POST['username'])) && (!empty($_POST['password']))){
			//DB Handler object is created so that the username and password can be processed by the login function.
			$dbhandler = new dbhandler();
			$msg = $dbhandler->login($_POST['username'],$_POST['password']);
			//Once the data is returned in variable msg, it is checked, as msg can be boolean or an array.
            if (is_array($msg)){
<<<<<<< HEAD
                //The data gets parsed back to the client posting the data as a JSON string.
            	echo json_encode($msg,JSON_PRETTY_PRINT );
=======
                echo json_encode($msg,JSON_PRETTY_PRINT );
>>>>>>> v1.7.1
            }else echo $msg;

		}else echo "Empty values given";



?>
