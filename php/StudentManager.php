<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 10/3/17
 * Time: 3:49 PM
 */

class StudentManager
{
    private $username;
    private $userID;
    private $commits;

    function __construct()
    {
        $this->username = Null;
        $this->userID = Null;
        $this->commits = array();
    }

    function setUserID($id){
        $this->userID = $id;
    }

    function setUsername($u){
        $this->username = $u;
    }

    function addCommit($commit){
        $temp = $this->commits;

        array_merge($temp,$commit);
        $this->commits = $temp;
    }

    function toString(){
        $data["Username"] = $this->username;
        $data["Commits"] = $this->commits;
        return $data;
    }

}