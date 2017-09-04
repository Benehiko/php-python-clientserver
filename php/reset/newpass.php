<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/8/17
 * Time: 11:34 PM
 */

require_once("../dbhandler.php");

$id = null;

if (isset($_GET['t']) && !empty($_GET['id'])){
    $dbhandler = new dbhandler();
    if ($dbhandler->verifyToken($_GET['id'],$_GET['t'])){
        $token = $_GET['t'];
        include('passwordReset.php');
    }else{
        echo "Token invalid or user does not exist";
    }

}else if (isset($_POST['password']) && !empty($_POST['password'])){
    $dbhandler = new dbhandler();
    if ($dbhandler->updatePassword($_POST['id'],$_POST['password'])){
        echo "Password reset";
    }else echo "Something went wrong";
}else echo "Nothing posted";