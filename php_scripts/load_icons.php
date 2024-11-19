<?php
header('Content-Type: application/json');

require_once 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['error' => 'Valid id is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT iconId, type, picture, notes, x_pos, y_pos FROM icons WHERE assignmentID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $icons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $icons]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
