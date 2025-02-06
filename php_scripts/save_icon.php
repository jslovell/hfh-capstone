<?php
header('Content-Type: application/json');
require 'db.php';

try {
    $iconId = $_POST['iconId'] ?? null;
    $assignmentID = $_POST['assignmentID'] ?? null;
    $type = $_POST['type'] ?? null;
    $notes = $_POST['notes'] ?? null;
    $x_pos = $_POST['x_pos'] ?? null;
    $y_pos = $_POST['y_pos'] ?? null;

    if (!$iconId || !$assignmentID || !$x_pos || !$y_pos) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Handle file upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/photos/'; //directory path
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); //create the directory if it doesn't exist
        }

        $photoName = time() . '_' . basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $photoName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $photoName; //path for database storage (file path or URL)
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
            exit;
        }
    }

    // Insert or update database record
    $query = "INSERT INTO icons (iconId, assignmentID, type, notes, picture, x_pos, y_pos)
              VALUES (?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE
              type = VALUES(type), notes = VALUES(notes), picture = COALESCE(VALUES(picture), picture), x_pos = VALUES(x_pos), y_pos = VALUES(y_pos)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$iconId, $assignmentID, $type, $notes, $photoPath, $x_pos, $y_pos]);

    echo json_encode(['success' => true, 'message' => 'Icon saved successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
