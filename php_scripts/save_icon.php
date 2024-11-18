<?php
require_once 'db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data (JSON)
    $input = file_get_contents('php://input');

    // Decode the JSON data
    $data = json_decode($input, true);

    // Check if decoding was successful
    if ($data === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    // Extract the fields from the decoded data
    $iconId = $data['iconId'];
    $assignmentID = $data['assignmentID'];
    $type = $data['type'];
    $picture = $data['photo'];
    $notes = $data['notes'];
    $x_pos = $data['x_pos'];
    $y_pos = $data['y_pos'];

    // Check if x_pos or y_pos are null and retrieve existing values if needed
    if ($x_pos === null || $y_pos === null) {
        $stmt = $conn->prepare("SELECT x_pos, y_pos FROM icons WHERE iconId = ?");
        $stmt->bind_param('s', $iconId);
        $stmt->execute();
        $stmt->bind_result($existing_x_pos, $existing_y_pos);
        $stmt->fetch();
        $stmt->close();

        // Use existing values if x_pos or y_pos are null
        $x_pos = $x_pos ?? $existing_x_pos ?? 0;
        $y_pos = $y_pos ?? $existing_y_pos ?? 0;
    }

    // Step 1: Check if the iconId already exists in the database
    $stmt = $conn->prepare("SELECT 1 FROM icons WHERE iconId = ?");
    $stmt->bind_param('s', $iconId);
    $stmt->execute();
    $stmt->store_result();  // Stores the result to check if any row exists

    if ($stmt->num_rows > 0) {
        // Step 2: If iconId exists, perform an update
        $stmt->close();
        $stmt = $conn->prepare("UPDATE icons SET assignmentID = ?, type = ?, picture = ?, notes = ?, x_pos = ?, y_pos = ? WHERE iconId = ?");
        $stmt->bind_param('isssiii', $assignmentID, $type, $picture, $notes, $x_pos, $y_pos, $iconId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Icon updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        // Step 3: If iconId does not exist, perform an insert
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO icons (iconId, assignmentID, type, picture, notes, x_pos, y_pos) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sisssii', $iconId, $assignmentID, $type, $picture, $notes, $x_pos, $y_pos);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Icon inserted successfully', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
