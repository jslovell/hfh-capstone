<?php

  // Uncomment/Comment out whatever variables you need.

  // (Production Database Variables)
  $servername = "localhost";
  $username = "hfh";
  $password = "hfh";
  $dbname = "hfh";

  //(Localhost Database Variables)
  // $servername = "localhost";
  // $username = "root";
  // $password = "";
  // $dbname = "hfh";


  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Establish database connection using PDO
  try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
  }

?>
