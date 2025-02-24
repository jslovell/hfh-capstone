<?php 

require_once "db.php";

if(isset($_POST['id'])){
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  $query = "DELETE FROM form_entries WHERE id = $id";
  if(mysqli_query($conn, $query)){
    echo "success";
  } else{
    echo "error";
  }
}

?>