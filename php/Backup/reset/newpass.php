<?php
/**
 * Created by PhpStorm.
 * User: benehiko
 * Date: 8/8/17
 * Time: 11:34 PM
 */

require_once("../dbhandler.php");

$id = null;

if (isset($_GET['t']) && !empty($_GET['id'])){
    $dbhandler = new dbhandler();
    if ($dbhandler->verifyToken($_GET['id'],$_GET['t'])){
        $id = $_GET['id'];
        echo '<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="../../css/style.css" rel="stylesheet" media="screen">
		<link href="../../color/default.css" rel="stylesheet" media="screen">
	</head>
	<body>
		<div class="container">
			<div class = "row">
				<div class = "col-md-12">
				<h2>Reset password</h2>
				<form id="formReset"  action="http://reset.anzen-learning.xyz/newpass.php" method="post">
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" placeholder="Password" name="password">
					</div>
					<div class="form-group">
						<label for="inputPasswordCheck">Re-enter password</label>
						<input type="password" class="form-control" id="inputPasswordCheck" placeholder="Re-enter password">
					</div>
					<div class="form-group">
					   <input type="hidden" class="form-control" id="id" name="id" value="'.$id.'">
                    </div>
					<input type="submit" value="Submit" class="btn btn-default" id="resetSubmit" >
				</form>
				</div>	
			</div>
		</div>
	</body>';

    }else{
        echo "Token invalid or user does not exist";
    }

}else if (isset($_POST['password']) && !empty($_POST['password'])){
    $dbhandler = new dbhandler();
    if ($dbhandler->updatePassword($_POST['id'],$_POST['password'])){
        echo "Password reset";
    }else echo "Something went wrong";
}else echo "Nothing posted";