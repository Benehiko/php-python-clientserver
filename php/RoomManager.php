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

<<<<<<< HEAD
    function getRoomID(){
        return $this::$roomID;
    }

    function getRoomName(){
        return $this::$roomName;
    }

    function addStudent($student){
        $s = new StudentManager();
        $s.setUsername($student["Username"]);

        //Add commit to an array for further processing
        $commit = array("CommitID"=>$student["Commit"], "CommitDescription"=>$student["CommitDescription"], "CommitDateTime"=>$student["CommitDateTime"]);
        $s.addCommit($commit);

        array_push($this::$students, $s);
    }

    function toString(){
        $array_return = array("RoomID"=>$this->getRoomID(), "RoomName"=>getRoomName(), "Students"=>$this::$students);
        return json_encode($array_return,JSON_PRETTY_PRINT );
    }
=======
    function setStudents($student){
        $s = new StudentManager();
        $s.setUsername($student["Username"]);
        $s.addCommit($student["Commit"]);

        array_push($this::$students, $s);
    }
>>>>>>> v1.7.1
}