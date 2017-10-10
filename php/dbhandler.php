<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/3/17
 * Time: 9:59 AM
 */

require('dbconnect.php');
require('emailhandler.php');
ini_set('display_errors', 'On');

class dbhandler
{

    private static $db;

    function __construct()
    {
        $this::$db = new dbconnect();
    }


    function getData($id){
        $AccountType = $this->checkAccountType($id);
        if ($AccountType["GroupID"] == 1) {
            $data = $this->getAdmin($id);
        } else {
            $data = $this->getStudent($id);
        }
        array_merge($AccountType,$data);
        return $AccountType;
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

    function getAdmin($id){
        /* Admin data consists of:
         *  1. Rooms + Room Data
         *  2. Students in each room
         *  3. Student Commits
         *  4. Student Comments
         *  5. Student Marks
         *  6.
         */

        //Initialise Variablese
        $roomID = Null;
        $roomName = NUll;
        $users = Null;
        $username = Null;
        $commitID = Null;
        $commitDatetime = Null;
        $commitDescription = Null;
        $count = 0;
        $student = array();

        //Firstly the admin needs the rooms and the studends belonging to each room.
        //A 2D array needs to be made or an Object containing an Array of students for each room.

        $Rooms = array();

        //Connect to Database
        $mysqli = $this::$db->connect();

        if ($stmt = $mysqli->prepare("SELECT roomID, roomName FROM room")){
            $stmt->execute();
            $stmt->bind_result($roomID,$roomName);

            while ($stmt->fetch()){
                $Rooms[$count] = new RoomManager();
                $Rooms[$count]->setRoomID($roomID);
                $Rooms[$count]->setRoomName($roomName);
                $count++;
            }
            $stmt->close();

            foreach ($Rooms as $room){
                $roomID = $room->getRoomID();
                if ($stmt = $mysqli->prepare("SELECT userID FROM room_user WHERE roomID = ?")) {
                    $stmt->bind_param("i", $roomID);
                    $stmt->execute();
                    $stmt->bind_result($userID);
                    $i = 0;
                    while ($stmt->fetch()){
                        $users[$i] = $userID;
                        $i++;
                    }

                    $stmt->close();
                }

                    if ($stmt_username = $mysqli->prepare("SELECT username FROM user WHERE userID =?")){
                        if ($stmt_commit = $mysqli->prepare("SELECT commitID, description, datetime FROM commit INNER JOIN commit_user ON commit.commitID = commit_user.commitID WHERE commit_user.userID = ?")){

                            for ($x=0;$x<=len($users);$x++){
                                $stmt_username->bind_param("i",$users[$x]);
                                $stmt_username->execute();
                                $stmt_username->bind_result($username);
                                $stmt_username->fetch();


                                $stmt_commit->bind_param("i",$users[$x]);
                                $stmt_commit->execute();
                                $stmt_commit->bind_result($commitID,$commitDescription,$commitDatetime);
                                $stmt_commit->fetch();

                                $student = array("Username"=>$username,"CommitID"=>$commitID,"CommitDescription"=>$commitDescription,"CommitDateTime"=>$commitDatetime);
                                $room->addStudent($student);
                            }
                            $stmt_username->close();
                            $stmt_commit->close();
                        }
                    }
            }

            $data = array("Usertype"=>"Admin","Username"=>$this::getUsername($id),"Rooms"=>$Rooms);
            return $data;
        }
        return false;
    }

    function getStudent($id){

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

                                                    if ($emailHandler->sendEmail($user, $msg, $subject))
                                                        return true;
                                                    else return "Email could not be sent";

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
        $ownr = $mysqli->real_escape_string($ownerID);
        $rmN = $mysqli->real_escape_string($roomName);

        if ($stmt = $mysqli->prepare("INSERT INTO room VALUES(?,?)")){
            $stmt->bind_param("is",$ownr, $rmN);
            try{
                $stmt->execute();
            }catch(Exception $e){
                return $e->getMessage();
            }
            return true;
        }else return "Could not insert into room";
        return false;
    }

    function addUserRoom($roomID, $users){
        $mysqli = $this::$db->connect();
        for ($k=0; $k<=len($users); $k++) {
            if ($stmt = $mysqli->prepare("INSERT INTO room_user VALUES (?,?)")) {
                $stmt->bind_param("ii", $roomID, $userID);
                $stmt->execute();
                $stmt->close();
                return true;
            }
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
}