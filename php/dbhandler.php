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

            if ($token == $hash) {
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

                        if (($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE userdataID = ?"))){
                            $stmt->bind_param("si",$token,$id);
                            if ($stmt->execute()){
                                $stmt->close();
                                $data = array("token"=>$token,"id"=>$id);
                                return $data;
                            }
                        }

                    }else return "Login Failed";

                }else return "Verify Email";
            }

        }
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
                                if ($stmt = $mysqli->prepare("INSERT INTO user_data(userdataID,verified,hash) VALUES(?,?,?)")) {
                                    $stmt->bind_param("iis", $id,$verified,$hash);
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

                                        }
                                    }return "Could not complete registration";
                                }

                            }
                        }

                    } else return "Could not create user";

                }
            }else return "User exists";
        }
        return "False";
    }
}