<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['owner_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$id = $_POST['id'] ?? '';

if (empty($id) || !is_numeric($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid menu item ID']);
    exit();
}

$id = (int)$id;
$owner_id = $_SESSION['owner_id'];

$stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $id, $owner_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Menu item not found or not owned by you']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
