<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/3/17
 * Time: 9:28 AM
 */

//require('dbconnect.php');
ini_set('display_errors', 'On');

class emailhandler
{

    private static $mysqli;

    function __construct($mysqli)
    {
        $this::$mysqli = $mysqli;
    }

    function sendVerification($userID, $userEmail){
        //Hash userID with UserEmail
        $data = $userID.$userEmail;
        $hash = hash("md5",$data,false);

        //save hash
        $mysqli = $this::$mysqli;
        if ($stmt = $mysqli->prepare("UPDATE user_data SET hash = ? WHERE id = ?")){
            $stmt->bind_param("si",$hash,$userID);
            $stmt->execute();
            $stmt->close();

            //return hash
            return $hash;
        }

        return false;

    }

    function sendEmail($username, $msg, $subject){
        $to = $username;
        $headers = "From: webmaster@anzen-learning.xyz" . "\r\n" .
            "Reply-To: admin@anzen-learning.xyz" . "\r\n" .
            "X-Mailer: PHP/".phpversion();

        if (mail($to, $subject, $msg, $headers))
            return true;
        else return false;
    }
}