<?php
require 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// === MENU ENDPOINTS ===
if ($action === 'get_menu') {
    $result = $conn->query("SELECT * FROM menu");
    $menu = ["bf" => [], "lunch" => [], "dinner" => []];
    while ($row = $result->fetch_assoc()) {
        $menu[$row['type']] = ["menu" => $row['menu_text'], "price" => $row['price']];
    }
    jsonResponse(["status" => "success", "data" => $menu]);
}

if ($action === 'update_menu') {
    if (($_SESSION['user_role'] ?? '') !== 'admin') jsonResponse(["status" => "error"]);
    
    $data = json_decode($_POST['menu_data'], true);
    foreach ($data as $type => $item) {
        $stmt = $conn->prepare("UPDATE menu SET menu_text = ?, price = ? WHERE type = ?");
        $stmt->bind_param("sis", $item['menu'], $item['price'], $type);
        $stmt->execute();
    }
    jsonResponse(["status" => "success", "message" => "Menu updated!"]);
}

// === REQUESTS ENDPOINTS ===
if ($action === 'submit_request') {
    $email = $_SESSION['user_email'] ?? 'unknown';
    $type = $_POST['type'] ?? '';
    $detail = $_POST['detail'] ?? '';
    $qty = $_POST['qty'] ?? 0;
    $cost = $_POST['cost'] ?? 0;
    $time = date('H:i A');

    $stmt = $conn->prepare("INSERT INTO requests (user_email, type, detail, qty, cost, time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $email, $type, $detail, $qty, $cost, $time);
    $stmt->execute();
    jsonResponse(["status" => "success"]);
}

if ($action === 'get_requests') {
    $result = $conn->query("SELECT * FROM requests ORDER BY id DESC");
    $reqs = [];
    while ($row = $result->fetch_assoc()) {
        $reqs[] = [
            "id" => $row['id'], "user" => $row['user_email'], 
            "type" => $row['type'], "detail" => $row['detail'], 
            "qty" => $row['qty'], "status" => $row['status'], "time" => $row['time']
        ];
    }
    jsonResponse(["status" => "success", "data" => $reqs]);
}

if ($action === 'complete_request') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $conn->prepare("UPDATE requests SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    jsonResponse(["status" => "success"]);
}

// === GATE PASS ENDPOINTS ===
if ($action === 'update_gate') {
    $email  = $_SESSION['user_email'] ?? 'unknown';
    $status = ($_POST['status'] ?? 'IN') === 'OUT' ? 'OUT' : 'IN';
    $time   = date('h:i A');

    $stmt = $conn->prepare("SELECT id FROM gate_passes WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        if ($status === 'OUT') {
            $upd = $conn->prepare("UPDATE gate_passes SET status = ?, leaving_time = ? WHERE user_email = ?");
        } else {
            $upd = $conn->prepare("UPDATE gate_passes SET status = ?, entered_time = ? WHERE user_email = ?");
        }
        $upd->bind_param("sss", $status, $time, $email);
        $upd->execute();
    } else {
        $leaving = $status === 'OUT' ? $time : '--:--';
        $entered = $status === 'IN'  ? $time : '--:--';
        $ins = $conn->prepare("INSERT INTO gate_passes (user_email, status, leaving_time, entered_time) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $email, $status, $leaving, $entered);
        $ins->execute();
    }
    jsonResponse(["status" => "success"]);
}

if ($action === 'get_gate_passes') {
    $result = $conn->query("SELECT * FROM gate_passes");
    $passes = [];
    while ($row = $result->fetch_assoc()) {
        $passes[] = ["user" => $row['user_email'], "status" => $row['status'], "leavingTime" => $row['leaving_time'], "enteredTime" => $row['entered_time']];
    }
    jsonResponse(["status" => "success", "data" => $passes]);
}

// === SOS ENDPOINTS ===
if ($action === 'trigger_sos') {
    $email = $conn->real_escape_string($_SESSION['user_email'] ?? 'unknown');
    $stmt = $conn->prepare("INSERT INTO sos_alerts (user_email) VALUES (?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    jsonResponse(["status" => "success"]);
}

if ($action === 'check_sos') {
    $result = $conn->query("SELECT * FROM sos_alerts WHERE status = 'Active' LIMIT 1");
    if ($result->num_rows > 0) {
        jsonResponse(["status" => "success", "active" => true]);
    }
    jsonResponse(["status" => "success", "active" => false]);
}

if ($action === 'clear_sos') {
    $conn->query("UPDATE sos_alerts SET status = 'Resolved' WHERE status = 'Active'");
    jsonResponse(["status" => "success"]);
}

jsonResponse(["status" => "error", "message" => "Invalid action!"]);
?>
