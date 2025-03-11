<?php 

require_once "db.php";

if(isset($_POST['id'])){
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  $query1 = "DELETE FROM form_entries WHERE id = $id";
  $query2 = "DELETE FROM icons WHERE assignmentID = $id";
  if(mysqli_query($conn, $query1) && mysqli_query($conn, $query2)){
    echo "success";
  } else{
    echo "error";
  }
}

?>
