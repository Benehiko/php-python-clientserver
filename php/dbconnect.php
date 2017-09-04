<?php
ini_set('display_errors', 'On');

class dbconnect{
	//db name
	private $db_name = "muteki";
	private $db_username = "muteki-user";
	private $db_pass = "r&eimA0JRna5";
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
