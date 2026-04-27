<?php
require 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'book_room') {
    if (!isset($_SESSION['user_email'])) {
        jsonResponse(["status" => "error", "message" => "Not logged in"]);
    }

    $email = $_SESSION['user_email'];
    $room = $_POST['room'] ?? '';
    $method = $_POST['method'] ?? '';
    $account_no = $_POST['account_no'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    $total = $_POST['total'] ?? '';

    $stmt = $conn->prepare("INSERT INTO bookings (user_email, room_name, method, account_no, transaction_id, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $email, $room, $method, $account_no, $transaction_id, $total);
    
    if ($stmt->execute()) {
        jsonResponse(["status" => "success", "message" => "Booking submitted successfully!"]);
    } else {
        jsonResponse(["status" => "error", "message" => "Failed to submit booking."]);
    }
}

if ($action === 'get_bookings') {
    // If admin, fetch all. If student, fetch only theirs.
    $role = $_SESSION['user_role'] ?? 'student';
    $email = $_SESSION['user_email'] ?? '';

    if ($role === 'admin') {
        $result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_email = ? ORDER BY id DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            "id" => $row['id'],
            "userEmail" => $row['user_email'],
            "room" => $row['room_name'],
            "method" => $row['method'],
            "senderNumber" => $row['account_no'],
            "transactionID" => $row['transaction_id'],
            "total" => $row['total'],
            "status" => $row['status'],
            "in" => explode(" ", $row['created_at'])[0]
        ];
    }
    jsonResponse(["status" => "success", "data" => $bookings]);
}

if ($action === 'update_status') {
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        jsonResponse(["status" => "error", "message" => "Unauthorized"]);
    }

    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? 'Pending';

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        jsonResponse(["status" => "success", "message" => "Status updated to $status"]);
    } else {
        jsonResponse(["status" => "error", "message" => "Failed to update status."]);
    }
}

jsonResponse(["status" => "error", "message" => "Invalid action!"]);
?>
