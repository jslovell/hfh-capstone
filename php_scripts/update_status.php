<?php
include "db.php"; 

if (isset($_POST['id']) && isset($_POST['assessmentStatus'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $assessmentStatus = mysqli_real_escape_string($conn, $_POST['assessmentStatus']);

    $query = "UPDATE form_entries SET assessmentStatus = '$assessmentStatus' WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
