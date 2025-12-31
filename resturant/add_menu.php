<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['owner_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$owner_id = $_SESSION['owner_id'];

if (empty($name) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
    exit();
}

if (!is_numeric($price) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid price greater than 0']);
    exit();
}

if (strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Item name is too long (max 100 characters)']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO menu_items (owner_id, name, price) VALUES (?, ?, ?)");
$stmt->bind_param("isd", $owner_id, $name, $price);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add menu item']);
}

$stmt->close();
$conn->close();
?>
