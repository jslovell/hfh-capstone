<?php

header('Location: https://hfh-capstone.bradley.edu/test_page.phtml');

$servername = "localhost";
$username = "hfh";
$password = "hfh";
$dbname = "hfh";

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

 // Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Make sure that state is abbreviation
$state = strtoupper($state);
$suffix = array("IOWA","ILLINOIS","ILL");
$abbreviation = array("IA", "IL", "IL");
$state = str_replace($suffix, $abbreviation, $state);

// City to uppercase
$city = strtoupper($city);

// Address to all uppercase
$address = strtoupper($address);

// Adds a space to the end of the address to signify end for abbreviation purposes
$address .=" ";

// Array of locations and their respective abbreviations
$suffix = array(" STREET ", " AVENUE ", " DRIVE ", " BEND ", " BOULEVARD ", " CIRCLE ", " COURT ", " HIGHWAY ", " LANE ", " ROAD ", " ROUTE ", " SQUARE ");
$abbreviation = array(" ST ", " AVE ", " DR ", " BND ", " BLVD ", " CIR ", " CT ", " HWY ", " LN ", " RD ", " RTE ", " SQ ");

// Replace suffix with abbreviation for searching our database
$address = str_replace($suffix, $abbreviation, $address);


// Create address to query (replace ' ' with % for extra space
$space = array(" ");
$perc = array("%");
$qry = str_replace($space, $perc, $address);
$qry .= $city;
$qry .= ",%";
$qry .= $state;
$qry .= "%";


$sql1 = "SELECT parcel_number FROM hfh.parcel WHERE address LIKE '$qry';";

$rs1 = mysqli_query($conn, $sql1);

if(mysqli_num_rows($rs1) == 0){
	 $sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, zip) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$zip');";
} else {
	$row = mysqli_fetch_assoc($rs1);

#'$row[parcel_number]'

	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, zip, parcel_number) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$zip', '$row[parcel_number]');";
} 


#if(mysqli_num_rows($rs1) == 0) {
#	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state');";
#} else {
#	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, parcel_number) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$rs1');";
#}

$rs2 = mysqli_query($conn, $sql2);


if($rs2){
    	echo "Success!";
}
else{
    echo "Error!";
}

mysqli_close($conn);



?>
