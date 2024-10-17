<?php

session_start();

require_once "db.php";

$uname = $_POST['uname'];
$psw = $_POST['psw'];

// Validate user exists
$sql = "SELECT password FROM hfh.accounts WHERE username='$uname';";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {

    // User does not exist
    // include failure.php
    header('Location: ../login_page_failure.phtml');
    exit();

} else {

    // User exists, fetch the hashed password
    $row = mysqli_fetch_assoc($result);
    $stored_hash = $row['password'];

    // Validate password
    $bool = password_verify($psw, $stored_hash);

    if($bool){
	$_SESSION['authenticated'] = true;
	// include succes.php
  	header('Location: ../appMenu.phtml');
	exit();
    } else {
	// include failure.php
	header('Location: ../login_page_failure.phtml');
	exit();
    }
}

mysqli_close($conn);

?>
