<?php
header('Content-Type: application/json');
require 'db.php'; // Ensure this file correctly connects to your database

// Check if assignmentID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["success" => false, "error" => "No assignment ID provided"]);
    exit;
}

$assignmentID = intval($_GET['id']);

// Debugging: Log received ID
error_log("Fetching icons for assignmentID: " . $assignmentID);

$sql = "SELECT iconId, assignmentID, type, picture, notes, x_pos, y_pos FROM icons WHERE assignmentID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL prepare error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $assignmentID);
$success = $stmt->execute();

if (!$success) {
    echo json_encode(["success" => false, "error" => "SQL execution error: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$icons = [];

while ($row = $result->fetch_assoc()) {
    $icons[] = $row;
}

// Debugging: Log query results
error_log("Icons fetched: " . json_encode($icons));

if (!empty($icons)) {
    echo json_encode(["success" => true, "data" => $icons]);
} else {
    echo json_encode(["success" => false, "error" => "No icons found for assignment ID " . $assignmentID]);
}

exit;