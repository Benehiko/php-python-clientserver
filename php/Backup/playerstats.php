<?php
    session_start();

	if (isset($_COOKIE['edu_stats']) && (!empty($_COOKIE['edu_stats'])))
	{
        echo $_COOKIE['edu_stats'];
    }
	else if (isset($_SESSION['loggedin']) && (!empty($_SESSION['loggedin'])))
	{
        $stats = $_SESSION['level'] .":" . $_SESSION['gamelevel'];
        echo $stats;
    }
	else
	{
        echo 'Not Logged In';
    }
?>
