<?php

// Connection to the SQL database
require_once "db.php";


// Relevant variables

$uname = $_POST['uname'];
$psw = $_POST['psw'];
$psw2 = $_POST['psw2'];

$hash = password_hash($psw, PASSWORD_DEFAULT);

// Check for a capital letter
$pswLow = strtolower($psw);


if($psw != $psw2){
	header('Location: ../new_user_failure.php');
	exit();
}
else if( (strlen($psw) < 8)
	or ($pswLow==$psw)
	or (!(preg_match('~[0-9]+~',$psw)))
	or (!(preg_match("[[`'\~!@# $*()<>,:;{}\|]]",$psw)))
){
		header('Location: ../new_user_simple.php');
		exit();
}
else {
	$sql = "INSERT INTO hfh.accounts(username, password) VALUES ('$uname', '$hash');";

	$rs = mysqli_query($conn, $sql);

	header('Location: ../index.php');
	exit();
}



?>
