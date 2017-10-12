<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/3/17
 * Time: 9:59 AM
 */

require('dbconnect.php');
require('emailhandler.php');
require('RoomManager.php');
ini_set('display_errors', 'On');

class dbhandler
{

    private static $db;

    function __construct()
    {
        $this::$db = new dbconnect();
    }


    function getData($id){
        $data = array();
        $final = null;
        $AccountType = $this->checkAccountType($id);

        if ($AccountType["GroupID"] == "1") {
            $data = $this->getAdmin($id);
        } else {
            $data = $this->getStudent($id);
        }
        $final = array("UserDetails"=>$AccountType,"RoomDetails"=>$data);
        return $final;
    }

    function checkAccountType($id){
        //Initialise Variables
        $groupID = null;
        $gdescription = null;
        $data = null;
        //First check if student or admin

        $mysqli = $this::$db->connect();

        if ($stmt = $mysqli->prepare("SELECT groupID FROM user_group WHERE userID = ?")){
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->bind_result($groupID);
            $stmt->fetch();
            $stmt->close();

            if ($stmt = $mysqli->prepare("SELECT description FROM `group` WHERE groupID = ?")){
                $stmt->bind_param("i",$groupID);
                $stmt->execute();
                $stmt->bind_result($gdescription);
                $stmt->fetch();
                $stmt->close();
                $data = array("GroupID"=>$groupID, "GroupDescription"=>$gdescription);
                return $data;
            }else return "Could not get description";
        }else return "Could not get groupID";
        return false;
    }

    function getUsername($id){
        $mysqli = $this::$db->connect();
        $username = null;
        if ($stmt = $mysqli->prepare("SELECT username FROM user WHERE id = ?")){
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();
            return $username;
        }
        return false;
    }

    function getRooms($id, $mysqli)
    {
        $roomID = null;
        $roomName = null;
        $rooms = array();

        if ($stmt = $mysqli->prepare("SELECT room.roomID, room.roomName FROM room INNER JOIN room_user ON room_user.userID = room_user.userID WHERE userID = ?;")) {
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->bind_result($roomID, $roomName);

            while ($stmt->fetch()) {
                array_push($rooms,array("RoomID"=>$roomID,"RoomName"=>$roomName));
            }
            $stmt->close();

            return $rooms;
        }
    }

    function getStudentIDRoom($Rooms, $mysqli){
        $userID = null;
        $students = array();
        $final = array();
        $roomID = null;
        $temp = array();

        foreach ($Rooms as $room){
            $roomID = $room["RoomID"];
            if ($stmt = $mysqli->prepare("SELECT userID FROM room_user WHERE roomID = ?")){
                $stmt->bind_param("i",$roomID);
                $stmt->execute();
                $stmt->bind_result($userID);
                while ($stmt->fetch()){

                    $data = $this->getStudentData($userID);
                    $student = array("UserID"=>$userID);
                    if (is_array($data)){
                        $temp = array_merge($student, $data);
                    }else{
                        return array("Data"=>$data);
                    }
                    array_push($students,$temp);
                }
            }
            $roomarr = array("RoomID"=>$room["RoomID"],"RoomName"=>$room["RoomName"], "Students"=>$students);
            array_push($final, $roomarr);
        }
        return $final;
    }

    function getStudentMarks($id){

        $mysqli = $this::$db->connect();
        $mark = null;

        if ($stmt = $mysqli->prepare("SELECT mark FROM user_marks WHERE userID = ?")){
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->bind_result($mark);
            $stmt->fetch();
            $stmt->close();

            $mysqli->close();

        }
        return $mark;
    }

    function getStudentMessages($id){
         $mysqli = $this::$db->connect();
         $comment = null;
         $comments = array();

         if ($stmt = $mysqli->prepare("SELECT `comments` FROM `room_data` WHERE `userID_fk` = ?")){
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($comment);
            while ($stmt->fetch()) {
                array_push($comments, $comment);
            }
            $stmt->close();
            $mysqli->close();
            return $comments;
         } 
    }

    function getStudentData($id)
    {
        $mysqli = $this::$db->connect();
        $student = array();
        $userID = null;
        $commitID = null;
        $commitDescription = null;
        $commitDatetime = null;
        $commits = array();
        $username = null;

        $mark = $this->getStudentMarks($id);
        if ($stmt = $mysqli->prepare("SELECT username FROM user WHERE id =?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            if ($stmt = $mysqli->prepare("SELECT commitID FROM commit_user WHERE userID = ?")) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($commitID);
                while ($stmt->fetch()) {
                    if ($stmt2 = $mysqli->prepare("SELECT description, `datetime` FROM `commit` WHERE commitID = ?")) {
                        $stmt2->bind_param("i", $commitID);
                        $stmt2->execute();
                        $stmt2->bind_result($commitDescription, $commitDatetime);
                        array_push($commits, array("CommitID" => $commitID, "CommitDescription" => $commitDescription, "CommitDateTime" => $commitDatetime));
                        $stmt2->close();
                    }
                }
                $stmt->close();
                $comments = $this->getStudentMessages($userID);
                $student = array("Username" => $username, "Mark"=>$mark, "Commits" => $commits, "Comments"=>$comments);

                }else return "Cannot get commits";
            }else return "Cannot get Username";
        return $student;
    }

    function getStudents($students){
        $mysqli = $this::$db->connect();

        $student = array();
        $userID = null;
        $commitID = null;
        $commitDescription = null;
        $commitDatetime = null;
        $final = array();


        if ($stmt_username = $mysqli->prepare("SELECT username FROM user WHERE userID =?")){
            if ($stmt_commit = $mysqli->prepare("SELECT `commit`.`commitID`, `commit`.`description`, `commit`.`datetime` FROM `commit` INNER JOIN `commit_user` ON `commit`.`commitID` = `commit_user`.`commitID` WHERE `commit_user`.`userID` = ?")){
                foreach ($students as $user){
                    $stmt_username->bind_param("i",$user["UserID"]);
                    $stmt_username->execute();
                    $stmt_username->bind_result($username);
                    $stmt_username->fetch();


                    $stmt_commit->bind_param("i",$user["UserID"]);
                    $stmt_commit->execute();
                    $stmt_commit->bind_result($commitID,$commitDescription,$commitDatetime);
                    $stmt_commit->fetch();

                    $student = array("Username"=>$username,"CommitID"=>$commitID,"CommitDescription"=>$commitDescription,"CommitDateTime"=>$commitDatetime);
                    array_push($final, $student);

                }
                $stmt_username->close();
                $stmt_commit->close();
            }else return "Could not get user data";
        }else return "Could not get username";
        return $final;
    }

    function getAdmin($id)
    {
        /* Admin data consists of:
         *  1. Rooms + Room Data
         *  2. Students in each room
         *  3. Student Commits
         *  4. Student Comments
         *  5. Student Marks
         *  6.
         */
        $mysqli = $this::$db->connect();
        $rooms = $this->getRooms($id, $mysqli);
        $students = $this->getStudentIDRoom($rooms, $mysqli);
        $mysqli->close();
        //$arr_students = $this->getStudents($students);
        return $students;

    }

    function getStudent($id){
        $mysqli = $this::$db->connect();
        $rooms = $this->getRooms($id,$mysqli);
        $students = $this->getStudentIDRoom($rooms,$mysqli);
       return $students;
    }

    function getArray($stmt){
        $count = 0;
        $data = array();
        $row = null;

        $stmt->bind_result($row);
        while ($stmt->fetch()){
            $data[$count] = $row;
            $count++;
        }
        return $data;
    }
    function test(){
        $mysqli = $this::$db->connect();
        $users = array();
        $count = 0;
        if ($stmt = $mysqli->prepare("SELECT id FROM user")){
            $stmt->execute();
            $stmt->bind_result($row);
            while ($stmt->fetch()){
                $count++;
                $users[$count] = $row;
            }

            $stmt->close();
        }else return "Something went wrong";
        return $users;
    }

    function uploadfile($id, $file){

        $mysqli = $this::$db->connect();
        $roomID = NULL;
        $null = NULL;

        if ($stmt = $mysqli->prepare("SELECT roomID FROM room_user WHERE userID = ?")){
            $stmt->bind_param("i",$id);
            if ($stmt->execute()){
                $stmt->bind_result($roomID);
                $stmt->fetch();
                $stmt->close();

                if ($stmt = $mysqli->prepare("INSERT INTO room_data Values(?,?,?)")){

                    $stmt->bind_param("iib",$roomID,$id,$null);
                    while (!feof($file)){
                        $stmt->send_long_data(0,fread($file,8192));
                    }
                    fclose($file);

                    if ($stmt->execute()){
                        $stmt->close();
                        return "File Stored";
                    }
                }
            }

        }
        return "Could not store file";


    }
    function verifyUser($id, $hash)
    {
        $mysqli = $this::$db->connect();
        $verified = 1;

        if ($stmt = $mysqli->prepare("SELECT verified FROM user_data WHERE userdataID =?")) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->bind_result($v);
                $stmt->fetch();
                $stmt->close();

                if ($v == 0) {
                    if ($stmt = $mysqli->prepare("SELECT hash FROM user_data WHERE userdataID = ?")) {
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $stmt->bind_result($h);
                        $stmt->fetch();
                        $stmt->close();

                        if ($hash == $h) {
                            if ($stmt = $mysqli->prepare("UPDATE user_data SET verified = ? WHERE userdataID = ?")) {
                                $stmt->bind_param("ii", $verified, $id);
                                if ($stmt->execute()) {
                                    $stmt->close();
                                    $mysqli->close();
                                    return true;
                                }else return "Could not update verified";
                            }
                        }
                        return "Hash does not match";
                    }
                } else return "Account has already been verified.";

            }
        }else return "Could not get verified";

        return false;

    }

    function logout($i,$to){

        $mysqli = $this::$db->connect();
        $t = "";
        $id = $mysqli->real_escape_string($i);
        $token = $mysqli->real_escape_string($to);
        if ($stmt = $mysqli->prepare("SELECT hash FROM user_data WHERE userdataID =?")){
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->bind_result($t);
            $stmt->fetch();
            $stmt->close();

            if ($token == $t){
                if($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE userdataID = ?")){
                    $t = "";
                    $stmt->bind_param("si",$t,$id);
                    $stmt->execute();
                    $stmt->close();
                    $mysqli->close();
                    return true;
                }
            }
        }
        return false;
    }

    function resetUser($email)
    {

        $mysqli = $this::$db->connect();
        $emailHandler = new emailhandler($mysqli);

        $e = $mysqli->real_escape_string($email);
        $token = null;

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE username = ?")) {
            $stmt->bind_param("s", $e);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count == 1) {
                if ($stmt = $mysqli->prepare("SELECT id FROM user WHERE username = ?")) {
                    $stmt->bind_param("s", $e);
                    $stmt->execute();
                    $stmt->bind_result($id);
                    $stmt->fetch();
                    $stmt->close();


                    if (($token = $emailHandler->sendVerification($id, $e)) != false) {

                        if ($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE id =?")) {
                            $stmt->bind_param("si", $token, $id);
                            $stmt->execute();
                            $stmt->close();


                            $msg = "Hi there user. Please click the link below to reset your password" .
                                "\nhttp://reset.anzen-learning.xyz/newpass.php?t=" . $token . "&id=" . $id;
                            $subject = "Password Reset Anzen-learning.xyz";
                            $emailHandler->sendEmail($e, $msg, $subject);
                            return true;

                        } else echo "Could not update token";

                    } else {
                        return false;
                    }
                }

            } else {
                echo "User does not exist";
                return false;
            }
        }

        return false;
    }

    function verifyToken($userID, $token)
    {
        $mysqli = $this::$db->connect();
        $id = $mysqli->real_escape_string($userID);
        $t = $mysqli->real_escape_string($token);

        if ($stmt = $mysqli->prepare("SELECT hash FROM user_data WHERE userdataID =?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($hash);
            $stmt->fetch();
            $stmt->close();

            if ($t == $hash) {
                $mysqli->close();
                return true;
            }
        }
        return false;
    }

    function updatePassword($token, $password)
    {
        $mysqli = $this::$db->connect();
        $t = $mysqli->real_escape_string($token);
        $pass = $mysqli->real_escape_string($password);

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM user_data WHERE hash = ?")) {
            $stmt->bind_param("i", $t);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->execute();
            $stmt->close();

            $newpass = password_hash($pass, PASSWORD_BCRYPT);

            if ($stmt = $mysqli->prepare("SELECT id FROM user_data WHERE hash = ?")) {
                $stmt->bind_param("s", $t);
                $stmt->execute();
                $stmt->bind_result($id);
                $stmt->fetch();
                $stmt->close();

                if ($stmt = $mysqli->prepare("UPDATE user SET password = ? WHERE id =?")) {
                    $stmt->bind_param("si", $newpass, $id);
                    $stmt->execute();
                    $stmt->close();

                    if ($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE id = ?")) {
                        $newhash = "";
                        $stmt->bind_param("si", $newhash, $id);
                        $stmt->execute();
                        $stmt->close();
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function login($u,$p){
        $mysqli = $this::$db->connect();

        $username = $mysqli->real_escape_string($u);
        $password = $mysqli->real_escape_string($p);

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE username = ?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($result);
            $stmt->fetch();
            $stmt->close();

            if ($result == 1) {
                if (($stmt = $mysqli->prepare("SELECT password,id FROM user WHERE username = ?"))) {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $stmt->bind_result($pass, $id);
                    $stmt->fetch();
                    $stmt->close();

                    if (($stmt = $mysqli->prepare("SELECT verified FROM user_data WHERE userdataID = ?"))) {
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $stmt->bind_result($verified);
                        $stmt->fetch();
                        $stmt->close();

                        if ($verified == 1) {
                            if (password_verify($password, $pass)) {

                                $token = password_hash($username, PASSWORD_BCRYPT);

                                if (($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE userdataID = ?"))) {
                                    $stmt->bind_param("si", $token, $id);
                                    if ($stmt->execute()){
                                        $stmt->close();
                                        $data = array("token" => $token, "id" => $id);
                                        $data2 = $this->checkAccountType($id);
                                        try{
                                            $final = array_merge($data2,$data);
                                            $mysqli->close();
                                            return $final;
                                        }catch(Exception $e){
                                            log($e->getMessage());
                                            return "Account type mishap";
                                        }
                                    }else return "Could not generate token";
                                }

                            }else return "Login Failed";

                        }else return "Verify Email";
                    }

                }
            }else return "User does not exist";
        }
        return false;
    }

    function registerUser($username, $password)
    {

        $mysqli = $this::$db->connect();
        $emailHandler = new emailhandler($mysqli);

        $user = $mysqli->real_escape_string($username);
        $pass = $mysqli->real_escape_string($password);
        $newpass = password_hash($pass, PASSWORD_BCRYPT);

        $verified = 0;
        $hash = 0;
        $id = 0;
        $groupID = 2;
        if (strtolower(substr($user,strpos("@", $user),strlen($user)) ==  "@monash.edu")){
            $groupID = 1;
        }else{
            $groupID = 2;
        }

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE username = ?")) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->bind_result($result);
            $stmt->fetch();
            $stmt->close();

            if ($result == 0) {
                if ($stmt = $mysqli->prepare("INSERT INTO user(username, password) VALUES(?,?)")) {
                    $stmt->bind_param("ss", $user, $newpass);
                    if ($stmt->execute()) {
                        $stmt->close();
                        if ($stmt = $mysqli->prepare("SELECT id from user WHERE username = ?")) {
                            $stmt->bind_param("s", $user);
                            if ($stmt->execute()) {
                                $stmt->bind_result($id);
                                $stmt->fetch();
                                $stmt->close();
                                if ($stmt = $mysqli->prepare("INSERT INTO user_data VALUES(?,?,?)")) {
                                    $stmt->bind_param("iis", $id,$verified,$hash);
                                    if ($stmt->execute()) {
                                        $stmt->close();

                                        if ($stmt = $mysqli->prepare("INSERT INTO user_group(groupID, userID) VALUES(?,?)")) {
                                            $stmt->bind_param("ii", $groupID, $id);
                                            if ($stmt->execute()) {
                                                $stmt->close();


                                                if (($hash = $emailHandler->sendVerification($id, $user)) != false) {

                                                    $msg = "Your account was successfully created!\n" .
                                                        "Here are your account details:" .
                                                        "\nUsername: " . $user .
                                                        "\nAccount Activation Link: http://sepam.anzen-learning.xyz/verify.php?v=" . $hash . "&id=" . $id .
                                                        "\n*Notice: According to the Protection of Personal Information Act (POPI) we are not allowed to view your password or email address.\n" .
                                                        "Your password is encrypted on the database. All emails concerning your account was automatically generated.";
                                                    $subject = "User " . $user . " registered!";

                                                    if ($emailHandler->sendEmail($user, $msg, $subject)) {
                                                        $mysqli->close();
                                                        return true;
                                                    }else return "Email could not be sent";

                                                }else return "No email was sent";
                                            }else return "Could not add user group";
                                        }else return "Could not add user group";
                                    }else return "Could not complete registration";
                                }

                            }
                        }

                    } else return "Could not create user";

                }
            }else return "User exists";
        }
        return false;
    }

    function createRoom($ownerID, $roomName){
        $mysqli = $this::$db->connect();
        $owner = $mysqli->real_escape_string($ownerID);
        $rName = $mysqli->real_escape_string($roomName);
        $roomID = null;
        $accounttype = $this->checkAccountType($ownerID);
        if ($accounttype["GroupID"] == "1") {
            if ($stmt = $mysqli->prepare("INSERT INTO room(roomName) VALUES(?)")) {
                $stmt->bind_param("s", $rName);
                if ($stmt->execute()) {
                    $stmt->close();
                    if ($stmt = $mysqli->prepare("SELECT roomID FROM room WHERE roomName = ?")) {
                        $stmt->bind_param("i", $rName);
                        $stmt->execute();
                        $stmt->bind_result($roomID);
                        $stmt->fetch();
                        $stmt->close();

                        if ($stmt = $mysqli->prepare("INSERT INTO room_user(roomID, userID) VALUES(?,?)")) {
                            $stmt->bind_param("ii", $roomID, $owner);
                            if ($stmt->execute()) {
                                $stmt->close();
                                $mysqli->close();
                                return true;
                            } else return "Could not complete.";
                        } else return "Could not complete room creation";
                    } else return "Could not get Room ID";
                } else return "Room already exists.";
            } else return "Could not insert into room";
        }else return "You do not have access to this command";
        return false;
    }

    function addUserRoom($roomID, $userID){
        $mysqli = $this::$db->connect();

        if ($stmt = $mysqli->prepare("INSERT INTO room_user(roomID, userID) VALUES (?,?)")) {
                $stmt->bind_param("ii", $roomID, $userID);
                $stmt->execute();
                $stmt->close();
                $mysqli->close();
                return true;
            }
        return false;
    }

    function removeUserRoom($roomID, $userID){
        $mysqli = $this::$db->connect();

        if ($stmt = $mysqli->prepare("DELETE FROM room_user WHERE userID = ? AND roomID = ?")){
            $stmt->bind_param("ii",$userID,$roomID);
            $stmt->execute();
            return true;
        }
        return false;
    }

    function addMarks($userID, $mark){
        $mysqli = $this::$db->connect();

        if ($stmt = $mysqli->prepare("INSERT INTO user_marks(userID, mark) VALUES(?,?)")){
            $stmt->bind_param("ii",$userID,$mark);
            $stmt->execute();
            return true;
        }else return "Cannot insert mark";
        return false;
    }

    function commitdata($id, $roomid, $msg, $description){
        $mysqli = $this::$db->connect();

        $commitID = null;


        if ($stmt = $mysqli->prepare("INSERT INTO room_data(roomID,userID_fk,comments) VALUES(?,?,?)")){
            $stmt->bind_param("iis",$roomid,$id,$msg);
            $stmt->execute();
            $stmt->close();

            if ($stmt = $mysqli->prepare("INSERT INTO `commit`(datetime,description) VALUES(NOW(),?)")){
                $stmt->bind_param("s",$description);
                $stmt->execute();
                $stmt->close();

                if ($stmt = $mysqli->prepare("SELECT commitID FROM `commit` ORDER BY commitID DESC LIMIT 1")){
                    $stmt->bind_result($commitID);
                    $stmt->fetch();
                    $stmt->close();
                }
                    if ($stmt = $mysqli->prepare("INSERT INTO commit_user(commitID,userID) VALUES(?,?)")){
                        $stmt->bind_param("ii",$commitID,$id);
                        $stmt->execute();
                        $stmt->close();
                        return true;
                    }
            }
        }
        return false;
    }
}