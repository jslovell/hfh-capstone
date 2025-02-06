<?php

//header('Location: ../test_page.php');

require_once "db.php";

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$layout = $_FILES["layout"]["name"];

// Home layout upload
$target_dir = "../uploads/layouts/";
$target_file = $target_dir . basename($_FILES["layout"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["layout"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["layout"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["layout"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["layout"]["name"])). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file: Error #".$_FILES["layout"]["error"];
  }
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
	 $sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, zip, layout) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$zip', '$layout');";
} else {
	$row = mysqli_fetch_assoc($rs1);

#'$row[parcel_number]'

	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, zip, parcel_number, layout) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$zip', '$row[parcel_number]', '$layout');";
}


#if(mysqli_num_rows($rs1) == 0) {
#	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state');";
#} else {
#	$sql2 = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, parcel_number) VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$rs1');";
#}

if ($uploadOk == 1) {
    $rs2 = mysqli_query($conn, $sql2);

    if($rs2){
        	echo "Success!";
    }
    else{
        echo "Error!";
    }

    // Gets the last inserted ID and then redirects to test_page.php with the ID
    $new_id = mysqli_insert_id($conn);
      header("Location: ../test_page.php?id=$new_id");
      exit();
}

mysqli_close($conn);

?>
