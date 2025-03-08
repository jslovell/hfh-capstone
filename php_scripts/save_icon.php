<?php
header('Content-Type: application/json');
require 'db.php';
$response = ["success" => false];

$iconId       = trim($_POST['iconId'] ?? '');
$assignmentID = (int)($_POST['assignmentID'] ?? 0);
$type         = trim($_POST['type'] ?? '');
$notes        = trim($_POST['notes'] ?? '');
$x_pos        = (int)($_POST['x_pos'] ?? 0);
$y_pos        = (int)($_POST['y_pos'] ?? 0);

$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/photos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $photoName  = time() . "_" . basename($_FILES['photo']['name']);
    $targetFile = $uploadDir . $photoName;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photoPath = $photoName;
    } else {
        $response['error'] = "File upload failed.";
        echo json_encode($response);
        exit;
    }
} else {
    $photoName = null;
}

$existingRow = null;
if ($iconId !== '') {
    $readQuery = "SELECT assignmentID, type, picture, notes, x_pos, y_pos
                  FROM icons
                  WHERE iconId = ?
                  LIMIT 1";
    if ($readStmt = $conn->prepare($readQuery)) {
        $readStmt->bind_param("s", $iconId);
        $readStmt->execute();
        $result = $readStmt->get_result();
        $existingRow = $result->fetch_assoc();
        $readStmt->close();
    }
}

if ($existingRow) {
    if ($photoPath === null) {
        $photoPath = $existingRow['picture'];
    }
    if ($notes === '') {
        $notes = $existingRow['notes'];
    }
    if ($type === '') {
        $type = $existingRow['type'];
    }
}

if ($existingRow) {
    // =========== UPDATE Path ==============
    $updateQuery = "
        UPDATE icons
        SET
          assignmentID = ?,
          type         = ?,
          picture      = ?,
          notes        = ?,
          x_pos        = ?,
          y_pos        = ?
        WHERE iconId   = ?
    ";
    if ($updateStmt = $conn->prepare($updateQuery)) {
        $updateStmt->bind_param(
            "isssiis",
            $assignmentID,
            $type,
            $photoPath,
            $notes,
            $x_pos,
            $y_pos,
            $iconId
        );
        if ($updateStmt->execute()) {
            $response['success'] = true;
            if ($photoPath) {
                $response['fileName'] = $photoPath;
            }
        } else {
            $response['error'] = "Database update error: " . $updateStmt->error;
        }
        $updateStmt->close();
    } else {
        $response['error'] = "Failed to prepare update statement.";
    }

} else {
    // =========== INSERT Path ==============
    $insertQuery = "
        INSERT INTO icons
          (iconId, assignmentID, type, picture, notes, x_pos, y_pos)
        VALUES
          (?, ?, ?, ?, ?, ?, ?)
    ";
    if ($insertStmt = $conn->prepare($insertQuery)) {
        $insertStmt->bind_param(
            "sisssii",
            $iconId,
            $assignmentID,
            $type,
            $photoPath,
            $notes,
            $x_pos,
            $y_pos
        );
        if ($insertStmt->execute()) {
            $response['success'] = true;
            if ($photoPath) {
                $response['fileName'] = $photoPath;
            }
        } else {
            $response['error'] = "Database insert error: " . $insertStmt->error;
        }
        $insertStmt->close();
    } else {
        $response['error'] = "Failed to prepare insert statement.";
    }
}

echo json_encode($response);
?>