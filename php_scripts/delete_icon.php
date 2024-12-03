<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['iconId'])) {
        echo json_encode(['success' => false, 'error' => 'iconId is required']);
        exit;
    }

    $iconId = $data['iconId'];

    try {
        $stmt = $conn->prepare("DELETE FROM icons WHERE iconId = ?");
        $stmt->bind_param('s', $iconId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Icon deleted successfully']);
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
