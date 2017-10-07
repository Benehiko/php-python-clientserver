<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 10/3/17
 * Time: 3:45 PM
 */

class RoomManager
{

    function __construct()
    {
        $this::$roomID = Null;
        $this::$roomName = Null;
        $this::$students = array();
    }

    function setRoomID($id){
        $this::$roomID = $id;
    }

    function setRoomName($name){
        $this::$roomName = $name;
    }

    function setStudents($student){
        $s = new StudentManager();
        $s.setUsername($student["Username"]);
        $s.addCommit($student["Commit"]);

        array_push($this::$students, $s);
    }
}