<?php
session_start();

// Connection to the SQL database
require_once 'db.php';

$errors = [];
$uname = $_POST['uname'] ?? '';
$psw = $_POST['psw'] ?? '';
$psw2 = $_POST['psw2'] ?? '';

// Function to validate the password
function validate_password($psw, $psw2) {
    global $errors;

    // Password confirmation check
    if ($psw != $psw2) {
        $errors['psw'][] = "Passwords do not match. ";
    }

    // Password length check
    if (strlen($psw) < 8 || strlen($psw2) < 8) {
        $errors['psw'][] = "Password must be at least 8 characters long. ";
    }

    // Password uppercase letter check
    if (strtolower($psw) == $psw) {
        $errors['psw'][] = "Password must contain at least one uppercase letter. ";
    }

    // Password number check
    if (!preg_match('~[0-9]+~', $psw)) {
        $errors['psw'][] = "Password must contain at least one number. ";
    }

    // Password special character check
    if (!preg_match("~[`'\~!@#$%^&*()<>:;{}\|]+~", $psw)) {
        $errors['psw'][] = "Password must contain at least one special character. ";
    }
}

// Validate the password
validate_password($psw, $psw2);

// Check if the username already exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE username = :username");
$stmt->bindParam(':username', $uname);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count > 0) {
    $errors['uname'][] = "Username already exists. Please choose a different username.";
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit; 
}

// If no errors proceed with inserting into the database
try {
    $stmt = $pdo->prepare("INSERT INTO accounts (username, password) VALUES (:username, :password)");

    $hashed_password = password_hash($psw, PASSWORD_DEFAULT);

    $stmt->bindParam(':username', $uname);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account successfully created.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'There was an error creating the account.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
