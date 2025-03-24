<?php

//header('Location: ../test_page.php');

require_once "db.php";

$errors = [];

$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$phone = preg_replace("/[^0-9]/", "", $phone);
$address = trim($_POST['address'] ?? '');
$city = $_POST['city'] ?? '';
$state = strtoupper(trim($_POST['state'] ?? ''));
$zip = $_POST['zip'] ?? '';
//$layout = $_FILES["layout"]["name"] ?? '';

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";

// Checking for correct submissions for State Abbreviations
$validStates = [
  "AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA",
  "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK",
  "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY"
];
if (!in_array($state, $validStates)) {
  $errors['state'] = "Invalid state abbreviation. Please enter a valid state abbreviation.";
}
if (!preg_match("/^\d{5}$/", $zip)) $errors['zip'] = "Invalid zip code format. Must be 5 numbers.";

// Home layout upload
$target_dir = "../uploads/layouts/";
$layoutName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', basename($_FILES['layout']['name']));
$target_file = $target_dir . $layoutName;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
if (file_exists($target_file)) {
  $errors['layout'] = "Sorry, file already exists. ";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["layout"]["size"] > 5000000) {
  $errors['layout'] = "Sorry, your file is too large. ";
  $uploadOk = 0;
}

// Allow certain file formats
if(!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
  $errors['layout'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
  $uploadOk = 0;
}

if(!empty($errors)){
  echo json_encode([
      "status" => "error",
      "errors" => $errors,
      "old_values" => [
          "firstname" => $firstname,
          "lastname" => $lastname,
          "email" => $email,
          "phone" => $phone,
          "address" => $address,
          "city" => $city,
          "state" => $state,
          "zip" => $zip
      ]
  ], JSON_FORCE_OBJECT);
  exit;
}

// if everything is ok, try to upload file
if ($uploadOk == 1) {
  if (move_uploaded_file($_FILES["layout"]["tmp_name"], $target_file)) {
      // File uploaded successfully
  } else {
      $errors['layout'] = "Sorry, there was an error uploading your file.";
      echo json_encode(["status" => "error", "errors" => $errors]);
      exit();
      // Error uploading file
  }
}

$sql = "INSERT INTO hfh.form_entries (firstname, lastname, email, phone, address, city, state, zip, layout) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $firstname, $lastname, $email, $phone, $address, $city, $state, $zip, $layoutName);

if ($stmt->execute()) {
  $new_id = mysqli_insert_id($conn);
  echo json_encode(["status" => "success", "new_id" => $new_id]);
  exit();
} else {
  echo json_encode(["status" => "error", "errors" => ["database" => "Database error."]]);
  exit();
}

$stmt->close();
mysqli_close($conn);
?>
