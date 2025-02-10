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
    $photoName = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        
        $photoName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', basename($_FILES['photo']['name']));
        $targetFile = $uploadDir . $photoName;

        //move the uploaded file to the uploads/photos directory
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
            exit;
        }
    }

    //use INSERT ... ON DUPLICATE KEY UPDATE to update the row if iconId already exists!
    $query = "
        INSERT INTO icons (iconId, assignmentID, type, notes, picture, x_pos, y_pos)
        VALUES (:iconId, :assignmentID, :type, :notes, :picture, :x_pos, :y_pos)
        ON DUPLICATE KEY UPDATE
        type = VALUES(type),
        notes = VALUES(notes),
        picture = IF(VALUES(picture) != '', VALUES(picture), picture),
        x_pos = VALUES(x_pos),
        y_pos = VALUES(y_pos)
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':iconId' => $iconId,
        ':assignmentID' => $assignmentID,
        ':type' => $type,
        ':notes' => $notes,
        ':picture' => $photoName,
        ':x_pos' => $x_pos,
        ':y_pos' => $y_pos,
    ]);

    echo json_encode(['success' => true, 'message' => 'Icon saved successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
