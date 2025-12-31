<?php
session_start();
include "../config/db.php";

// Check if owner is logged in
if (!isset($_SESSION['owner_id'])) {
    echo json_encode([]);
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Validate owner_id is numeric
if (!is_numeric($owner_id)) {
    echo json_encode([]);
    exit();
}

// Fetch menu items for this owner using prepared statement
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE owner_id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode($menu);

$stmt->close();
$conn->close();
?>
