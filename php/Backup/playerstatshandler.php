<?php
ini_set('display_errors', 'On');

	session_start();
    require_once('dbconnect.php');
	$level = null;
	$gamelevel = null;
	$mysqli = null;

   if ((isset($_POST['level'])) && (isset($_POST['gamelevel'])) && (!empty($_POST['level'])) && (!empty($_POST['gamelevel'])))
	{
        init();
		$studentId = getStudentID();
		if ($studentId != false){
			if (getStats($studentId) != false){
				if ($_POST['level'] > $level){
					updateLevel($studentId);
				}
				if ($_POST['gamelevel'] > $gamelevel){
					updateGamelevel($studentId);
				}

				if ($level == 12){
					if (getEmailSent($studentId) == false){
					$to = "admin@anzen-learning.xyz";
					$message = "Student completed the task with the following details: \nEmail: ".$_SESSION['username']."\nStudent ID: ".$studentId;
					$headers = "From: webmaster@anzen-learning.xyz" . "\r\n" .
    								   "Reply-To: admin@anzen-learning.xyz" . "\r\n" .
   									   "X-Mailer: PHP/".phpversion();
					$subject = "FIT1050 Lecture - User ".$_COOKIE['edu_LOGIN']." finished task";
					mail($to, $subject, $message, $headers);
					updateEmailSent($studentId);
					}					
				}

				$stats = $_POST['level'].":".$_POST['gamelevel'];
				setcookie("edu_stats",$stats,time()+10800,'../');
				echoData();
			}else{
				echo "Cant get stats";
			}
		}else{
			echo "Cant get student Id";
		}
	}else{
		echo "Empty data sent";
	}    

	function init(){
		$db = new dbconnect();
		$GLOBALS['mysqli'] = $db->connect();
	}

	function updateEmailSent($studentId){
		$emailsent = true;
		if ($stmt = $GLOBALS['mysqli']->prepare("UPDATE user_data SET emailsent = ? WHERE id = ? ")){
               $stmt->bind_param("ii",$emailsent,$studentId);
			   $stmt->execute();
               $stmt->close();               
           }

	}

	function getEmailSent($studentId){
		if (($stmt = $GLOBALS['mysqli']->prepare("SELECT emailsent FROM user_data WHERE id = ?"))){
						$stmt->bind_param("i",$studentId);
						$stmt->execute();
						$stmt->bind_result($emailsent);
						$stmt->fetch();
						$stmt->close();

			return $emailsent;
		}
	}

	function getStats($studentId){
		 if (($stmt = $GLOBALS['mysqli']->prepare("SELECT level,gamelevel FROM user_data WHERE id = ?"))){
						$stmt->bind_param("i",$studentId);
						$stmt->execute();
						$stmt->bind_result($GLOBALS['level'],$GLOBALS['gamelevel']);
						$stmt->fetch();
						$stmt->close();

			return true;
		}else{
			return false;
		}	
	}


	function updateLevel($studentId){
		   if ($stmt = $GLOBALS['mysqli']->prepare("UPDATE user_data SET level = ? WHERE id = ? ")){
               $stmt->bind_param("ii",$_POST['level'],$studentId);
			   $stmt->execute();
               $stmt->close();

               $_SESSION['level'] = $_POST['level'];
           }
				
	}

	function updateGamelevel($studentId){
	    if ($stmt = $GLOBALS['mysqli']->prepare("UPDATE user_data SET gamelevel = ? WHERE id = ?")){
		    $stmt->bind_param("ii",$_POST['gamelevel'],$studentId);
		    $stmt->execute();
			$stmt->close();

			$_SESSION['gamelevel'] = $_POST['gamelevel'];
		}
		
	}

	function getStudentID(){
		if (($stmt = $GLOBALS['mysqli']->prepare("SELECT id FROM users WHERE username = ?"))){
				$stmt->bind_param("s",$_COOKIE['edu_LOGIN']);
				$stmt->execute();
				$stmt->bind_result($studentId);
				$stmt->fetch();
				$stmt->close();

				return $studentId;
		}
		return false;
	}

	function echoData(){
		$data = $GLOBALS['level'].":".$GLOBALS['gamelevel'];
		setcookie("edu_stats",$data,time()+10800,'../');
		echo $data;
	}

  
				

?>
