<?php
	
  // Uncomment/Comment out whatever variables you need.

  // (Production Database Variables)
  // $servername = "localhost";
  // $username = "hfh";
  // $password = "hfh";
  // $dbname = "hfh";
  
  // (Localhost Database Variables) 
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "hfh";


  $conn = mysqli_connect($servername, $username, $password, $dbname);

  if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
  }
 
?>