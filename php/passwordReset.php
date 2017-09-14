<?php

if (isset($_GET['t'])){


    echo '<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<meta charset="utf-8" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="../css/style.css" rel="stylesheet" media="screen">
	<link href="../color/default.css" rel="stylesheet" media="screen">
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
					<input type="hidden" class="form-control" id="id" name="t" value="'.$_GET['t'].'">
				</div>
				<input type="submit" value="Submit" class="btn btn-default" id="resetSubmit" >
			</form>
		</div>
	</div>
</div>
</body>';

}

?>
