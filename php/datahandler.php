<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 9/30/17
 * Time: 8:21 PM
 */
require_once('dbhandler.php');

    if ($_POST['Action'] == "CreateRoom"){
        $roomName = $_POST['roomName'];
        $ownerID = $_POST['ownerID'];
        $dbhandler = new dbhandler();
        $msg = $dbhandler->createRoom($ownerID, $roomName);
        if ($msg){
            echo true;
        }else echo $msg;
    }
    if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['file']) && !empty($_POST['file'])){
        $data = fopen($_POST['file'], 'rb');
        $id = $_POST['id'];
        $dbhandler = new dbhandler();
        $dbhandler->uploadfile($id,$data);
    }