<?php 

require_once "db.php";

if(isset($_POST['id'])){
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  $result = mysqli_query($conn, "SELECT layout FROM form_entries WHERE id = $id");
  if ($row = mysqli_fetch_assoc($result)){
    $layoutName = $row['layout'];
    $filePath = "../uploads/layouts/" . $layoutName;
    if(file_exists($filePath)){
      unlink($filePath);
    }
  }
  
  $query1 = "DELETE FROM form_entries WHERE id = $id";
  $query2 = "DELETE FROM icons WHERE assignmentID = $id";
  if(mysqli_query($conn, $query1) && mysqli_query($conn, $query2)){
    echo "success";
  } else{
    echo "error";
  }
}

?>
