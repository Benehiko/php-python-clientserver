<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/8/17
 * Time: 11:03 PM
 */
ini_set('display_errors', 'On');

require_once('dbhandler.php');

if (isset($_POST['email']) && !empty($_POST['email'])){
    $email = $_POST['email'];

    $dbhandler = new dbhandler();
    if ($dbhandler->resetUser($email)){
        echo "Success";
    }else echo "Failed";

}