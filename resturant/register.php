<?php
include "../config/db.php";

// Check if POST data is set
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    echo "All fields are required";
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Basic validation
if (empty($name) || empty($email) || empty($password)) {
    echo "All fields are required";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
    exit;
}

if (strlen($password) < 6) {
    echo "Password must be at least 6 characters long";
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM restaurants WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "Email already registered";
    $stmt->close();
    exit;
}
$stmt->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert using prepared statement
$stmt = $conn->prepare("INSERT INTO restaurants (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Registration failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
