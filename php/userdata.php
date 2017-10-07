<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 10/7/17
 * Time: 4:39 PM
 */
require_once("dbhandler.php");

if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['token']) && !empty($_POST['token'])){
    $dbhandler = new dbhandler();
    $data = null;

    if ($dbhandler->verifyToken($_POST['id'], $_POST['token'])){
        $id = $_POST['id'];
        $data = $dbhandler->getData($id);
    }else{
        $data = false;
    }
    echo $data;
}