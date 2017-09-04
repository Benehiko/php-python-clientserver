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

    function __construct(){
        $this::$db = new dbconnect();
    }
   function verifyUser($id, $hash){
        $mysqli = $this::$db->connect();
        $verified = 1;

        if ($stmt = $mysqli->prepare("SELECT verified FROM user_data WHERE id =?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($v);
            $stmt->fetch();
            $stmt->close();

            if ($v == 0) {
                if ($stmt = $mysqli->prepare("SELECT hash FROM user_data WHERE id = ?")) {
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->bind_result($h);
                    $stmt->fetch();
                    $stmt->close();

                    if ($hash == $h) {
                        if ($stmt = $mysqli->prepare("UPDATE user_data SET verified = ? WHERE id = ?")) {
                            $stmt->bind_param("ii", $verified, $id);
                            $stmt->execute();
                            $stmt->close();
                            return true;
                        }
                    }
                }
            }else{
                echo "Account has already been verified.";
                return false;
            }
        }

        return false;

    }

    function resetUser($email){

        $mysqli = $this::$db->connect();
        $emailHandler = new emailhandler($mysqli);

        $e = $mysqli->real_escape_string($email);
        $token = null;

        if ($stmt =  $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?")){
            $stmt->bind_param("s",$e);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count == 1){
                if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?")){
                    $stmt->bind_param("s",$e);
                    $stmt->execute();
                    $stmt->bind_result($id);
                    $stmt->fetch();
                    $stmt->close();



                    if (($token = $emailHandler->sendVerification($id,$e)) != false){

                        if ($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE id =?")){
                            $stmt->bind_param("si",$token,$id);
                            $stmt->execute();
                            $stmt->close();


                            $msg = "Hi there user. Please click the link below to reset your password".
                                "\nhttp://reset.anzen-learning.xyz/newpass.php?t=".$token."&id=".$id;
                            $subject = "Password Reset Anzen-learning.xyz";
                            $emailHandler->sendEmail($e,$msg,$subject);
                            return true;

                        }else echo "Could not update token";

                    }else{
                        return false;
                    }
                }

            }else{
                echo "User does not exist";
                return false;
            }
        }

        return false;
    }

    function verifyToken($userID, $token){
        $mysqli = $this::$db->connect();
        $id = $mysqli->real_escape_string($userID);
        $t = $mysqli->real_escape_string($token);

        if ($stmt = $mysqli->prepare("SELECT hash FROM user_data WHERE id =?")){
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($hash);
            $stmt->fetch();
            $stmt->close();

            if ($t == $hash){
                return true;
            }
        }
        return false;
    }

    function updatePassword($userID, $password){
        $mysqli = $this::$db->connect();
        $id = $mysqli->real_escape_string($userID);
        $pass = $mysqli->real_escape_string($password);

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE id = ?")){
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->execute();
            $stmt->close();

            $newpass = password_hash($pass,PASSWORD_BCRYPT);

            if ($stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id =?")){
                $stmt->bind_param("si",$newpass,$id);
                $stmt->execute();
                $stmt->close();
                return true;
            }
        }
        return false;
    }

    function registerUser($username, $password){

        $mysqli = $this::$db->connect();
        $emailHandler = new emailhandler($mysqli);
        $level = 0;
        $gamelevel = 1;
        $emailsent = false;
        $id = 0;
        $result = 1;
        $verified = 0;
        $hash = "";

        $user = $mysqli->real_escape_string($username);
        $pass = $mysqli->real_escape_string($password);
        $newpass = password_hash($pass, PASSWORD_BCRYPT);

        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?")){
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($result);
            $stmt->fetch();
            $stmt->close();

            if($result > 0){
                echo "User exists";
                return false;
            }
        }else return "SQL Failed";

        if ($stmt = $mysqli->prepare("INSERT INTO users(username, password) VALUES(?,?)")){
            $stmt->bind_param("ss",$user,$newpass);
            if ($stmt->execute()){
                if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?")){
                    $stmt->bind_param("s",$user);
                    $stmt->execute();
                    $stmt->bind_result($id);
                    $stmt->fetch();
                    $stmt->close();

                    if ($stmt =$mysqli->prepare("INSERT INTO user_data(level, gamelevel, id,emailsent,hash,verified) VALUES (?,?,?,?,?,?)")){
                        $stmt->bind_param("iiiisi",$level,$gamelevel,$id,$emailsent,$hash,$verified);
                        $stmt->execute();
                        $stmt->close();
                        $hash = $emailHandler->sendVerification($id,$user);

                        $msg = "Your account was successfully created!\n".
                            "Here are your account details:".
                            "\nUsername: ".$username.
                            "\nAccount Activation Link: http://verify.anzen-learning.xyz/verify.php?v=".$hash."&id=".$id.
                            "\n*Notice: According to the Protection of Personal Information Act (POPI) we are not allowed to view your password or email address.\n".
                            "Your password is encrypted on the database. All emails concerning your account was automatically generated.";
                        $subject = "User ".$username." registered!";

                        if ($emailHandler->sendEmail($user, $msg, $subject))
                            return true;
                        else return false;
                    }
                }

            }else return "Could not execute";
        }else return "Could not insert values.";
        return "Something happened.";
    }

}