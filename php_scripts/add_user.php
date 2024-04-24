<?php

// Connection to the SQL database
$servername = "localhost";
$username = "hfh";
$password = "hfh";
$dbname = "hfh";

$conn = mysqli_connect($servername, $username, $password, $dbname);




// Relevant variables

$uname = $_POST['uname'];
$psw = $_POST['psw'];
$psw2 = $_POST['psw2'];

$hash = password_hash($psw, PASSWORD_DEFAULT);

// Check for a capital letter
$pswLow = strtolower($psw);


if($psw != $psw2){
	header('Location: https://hfh-capstone.bradley.edu/new_user_failure.phtml');
	exit();
}
else if( (strlen($psw) < 8) 
	or ($pswLow==$psw) 
	or (!(preg_match('~[0-9]+~',$psw)))
	or (!(preg_match("[[`'\~!@# $*()<>,:;{}\|]]",$psw)))
){
		header('Location: https://hfh-capstone.bradley.edu/new_user_simple.phtml');
		exit();
}
else {
	$sql = "INSERT INTO hfh.accounts(username, password) VALUES ('$uname', '$hash');";

	$rs = mysqli_query($conn, $sql);

	header('Location: /login_page.phtml');
	exit();
}

mysqli_close($conn);

?>
