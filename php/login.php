<?php
	session_start();
	require('dbconnect.php');
	require('password.php');

	if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){

			$db = new DBConnect();
			$mysqli = $db->connect();
			$_SESSION['loggedin'] = false;
			
			if (($stmt = $mysqli->prepare("SELECT password,id FROM users WHERE username = ?"))) {
				$stmt->bind_param("s",$_POST['username']);
				$stmt->execute();
				$stmt->bind_result($pass,$id);
				$stmt->fetch();
				$stmt->close();
                //echo 'Password: '.password_hash($_POST['password'], PASSWORD_BCRYPT).'   =   '.$pass;

				if (($stmt = $mysqli->prepare("SELECT verified FROM user_data WHERE id = ?"))){
					$stmt->bind_param("i", $id);
					$stmt->execute();
					$stmt->bind_result($verified);
					$stmt->fetch();
					$stmt->close();

					if ($verified == 1){
                        if (password_verify($_POST['password'], $pass)){

                            if (($stmt = $mysqli->prepare("SELECT level,gamelevel FROM user_data WHERE id = ?"))){
                                $stmt->bind_param("i",$id);
                                $stmt->execute();
                                $stmt->bind_result($level,$gamelevel);
                                $stmt->fetch();
                                $stmt->close();

                                setcookie("edu_LOGIN", $_POST['username'], time()+10800, '../');
                                $_SESSION['loggedin'] = true;
                                $_SESSION['username'] = $_POST['username'];
                                $_SESSION['level'] = $level;
                                $_SESSION['gamelevel'] = $gamelevel;
                                $stats = $level.":".$gamelevel;
                                setcookie("edu_stats",$stats,time()+10800,'../');
                                echo "loggedin";
                            }else{
                                echo "Login Failed";
                            }

                        }else{
                            echo "Login Failed";
                        }
					}else echo "Verify Email";
				}

			}else{
				echo "Login failed";
			}
		}else{
			echo "Empty values given";
		}


?>
