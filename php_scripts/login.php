<?php
session_start();
require_once "db.php";

header('Content-Type: application/json');

$uname = $_POST['uname'] ?? '';
$psw = $_POST['psw'] ?? '';
$errors = [];

// Check if username exists and fetch the password hash
$sql = "SELECT password FROM accounts WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database query failed.'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $uname);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    // Username doesn't exist
    $errors['uname'][] = "Username does not exist. Please try again or create a new account.";
} else {
    // Username exists fetch the password
    mysqli_stmt_bind_result($stmt, $hashed_password);
    mysqli_stmt_fetch($stmt);

    // Verify password
    if (!password_verify($psw, $hashed_password)) {
        $errors['psw'][] = "Incorrect password. Please try again.";
    }
}

mysqli_stmt_close($stmt);

// Returns any errors
if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

// If everything passes log the user in
$_SESSION['authenticated'] = true;
$_SESSION['username'] = $uname;

echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
exit;
