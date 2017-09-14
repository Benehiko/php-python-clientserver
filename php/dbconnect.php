<?php
ini_set('display_errors', 'On');

class dbconnect{
	//db name
	private $db_name = "zangetsu";
	private $db_username = "sepam";
	private $db_pass = "cBxI0HU2a9uf";
  	private $db_ip = "localhost";

  public function connect(){
	 $mysqli = new mysqli($this->db_ip,$this->db_username,$this->db_pass,$this->db_name);
   if ($mysqli->connect_errno){
				echo "Failed to connect to db ".$mysqli->connect_errno;
	 }
	 return $mysqli;
  }	

}
?>
