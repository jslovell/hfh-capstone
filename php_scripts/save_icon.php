<?php
header('Content-Type: application/json');
require 'db.php';
$response = ["success" => false];

// Parse POST fields:
$iconId       = trim($_POST['iconId'] ?? '');
$assignmentID = (int)($_POST['assignmentID'] ?? 0);
$type         = trim($_POST['type'] ?? '');
$notes        = trim($_POST['notes'] ?? '');
$x_pos        = (int)($_POST['x_pos'] ?? 0);
$y_pos        = (int)($_POST['y_pos'] ?? 0);

$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = '../uploads/photos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $photoName  = time() . "_" . basename($_FILES['photo']['name']);
    $targetFile = $uploadDir . $photoName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photoPath = "uploads/photos/" . $photoName;
    } else {
        $response['error'] = "File upload failed.";
        echo json_encode($response);
        exit;
    }
}

if ($iconId !== '') {
    // Check if there's already a row with this iconId
    $readQuery = "SELECT picture, notes FROM icons WHERE iconId = ? LIMIT 1";
    if ($readStmt = $conn->prepare($readQuery)) {
        $readStmt->bind_param("s", $iconId);
        $readStmt->execute();
        $readStmt->bind_result($existingPicture, $existingNotes);
        if ($readStmt->fetch()) {
            if ($photoPath === null) {
                $photoPath = $existingPicture;
            }
            if ($notes === '') {
                $notes = $existingNotes;
            }
        }
        $readStmt->close();
    }
}

// Attempt to update first
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

// Prepare and execute the UPDATE
if ($updateStmt = $conn->prepare($updateQuery)) {
    $updateStmt->bind_param(
        "isssiis",
        $assignmentID,
        $type,
        $photoPath, // might be null or old image path
        $notes,     // might be empty or old notes
        $x_pos,
        $y_pos,
        $iconId
    );
    $updateStmt->execute();
    $affected = $updateStmt->affected_rows;
    $updateStmt->close();
} else {
    $response['error'] = "Failed to prepare update statement.";
    echo json_encode($response);
    exit;
}

// If the UPDATE affects 0 rows, then we INSERT as new row
if ($affected === 0) {
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
        } else {
            $response['error'] = "Database insert error: " . $insertStmt->error;
        }
        $insertStmt->close();
    } else {
        $response['error'] = "Failed to prepare insert statement.";
    }
} else {
    $response['success'] = true;
}

echo json_encode($response);
?>