<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/3/17
 * Time: 9:55 AM
 */
require_once('../dbhandler.php');
ini_set('display_errors', 'On');

if ((isset($_GET['v'])) && (!empty($_GET['v'])) && (isset($_GET['id']))){
    $hash = $_GET['v'];
    $id = $_GET['id'];
    $dbhandler = new dbhandler();
    if ($dbhandler->verifyUser($id,$hash)) {
        echo "Verified";
    }else echo "Could not verify";

    echo "<html><body><script>window.setTimeout(5000);window.location.assign('http://anzen-learning.xyz');</script></body></html>";


}else echo "Something went wrong. Please contact the website administrator at: admin@anzen-learning.xyz";
?>