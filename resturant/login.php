<?php
session_start();
include "../config/db.php";

// Check if POST data is set
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Basic validation
if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, name, password FROM restaurants WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['owner_id'] = $row['id'];
        $_SESSION['owner_name'] = $row['name'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Wrong password']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email not found']);
}

$stmt->close();
$conn->close();
?>
