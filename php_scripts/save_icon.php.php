<?php
require_once './php_scripts/db.php';

// Icon schema
$assignmentID = $_POST['assignmentID'];
$type = $_POST['type'];
$notes = $_POST['notes'];
$x_pos = $_POST['x_pos'];
$y_pos = $_POST['y_pos'];
$local_idx = $_POST['local_idx'];

// Handle picture upload
$picture_path = null;
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
    $picture_path = 'uploads/pictures/' . basename($_FILES['picture']['name']);
    move_uploaded_file($_FILES['picture']['tmp_name'], $picture_path);
}

$sql = "INSERT INTO icons (assignmentID, type, picture, notes, x_pos, y_pos, local_idx) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isssiii', $assignmentID, $type, $picture_path, $notes, $x_pos, $y_pos, $local_idx);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
$stmt->close();
mysqli_close($conn);
?>
