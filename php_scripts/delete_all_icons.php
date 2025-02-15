<?php
require_once 'db.php'; // Ensure this includes the correct database connection details

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['assignmentID'])) {
        echo json_encode(['success' => false, 'error' => 'assignmentID is required']);
        exit;
    }

    $assignmentID = $data['assignmentID'];

    try {
        // Prepare and execute the SQL DELETE statement
        $stmt = $conn->prepare("DELETE FROM icons WHERE assignmentID = ?");
        $stmt->bind_param('i', $assignmentID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'All icons deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
