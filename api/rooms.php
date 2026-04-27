<?php
require 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_rooms') {
    $result = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    jsonResponse(["status" => "success", "data" => $rooms]);
}

if ($action === 'add_room') {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';

    $stmt = $conn->prepare("INSERT INTO rooms (name, type, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $type, $price, $image);
    
    if ($stmt->execute()) {
        jsonResponse(["status" => "success", "message" => "Room added successfully!"]);
    } else {
        jsonResponse(["status" => "error", "message" => "Failed to add room."]);
    }
}

if ($action === 'delete_room') {
    $id = $_POST['id'] ?? 0;
    
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(["status" => "success", "message" => "Room deleted successfully!"]);
    } else {
        jsonResponse(["status" => "error", "message" => "Failed to delete room."]);
    }
}

jsonResponse(["status" => "error", "message" => "Invalid action!"]);
?>
