<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 10/3/17
 * Time: 3:45 PM
 */

class RoomManager
{
    private $roomID;
    private $roomName;
    private $students;

    function __construct()
    {
        $this->roomID = null;
        $this->roomName = null;
        $this->students = array();
    }

    function setRoomID($id){
        $this->roomID = $id;
    }
    function getRoomID(){
        return $this->roomID;
    }

    function getRoomName(){
        return $this->roomName;
    }
    function setRoomName($name){
        $this->roomName = $name;
    }

    function getStudents(){
        return $this->students;
    }

    function addStudent($student){
        $s = new StudentManager();
        $s.setUsername($student["Username"]);
        $s.addCommit($student["Commit"]);

        array_push($this->students, $s);
    }
}