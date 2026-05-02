<?php
session_start();

// ============================================================
//  DATABASE CONFIGURATION
//  IS_LIVE = false  → runs on your local XAMPP
//  IS_LIVE = true   → runs on InfinityFree live server
// ============================================================

define('IS_LIVE', true); // ✅ Changed to TRUE for InfinityFree live server

if (IS_LIVE) {
    // ── LIVE SERVER (InfinityFree) ──────────────────────────
    $host   = 'sql301.infinityfree.com';   // ✅ Your MySQL Hostname
    $user   = 'if0_41771040';              // ✅ Your MySQL Username
    $pass   = 'Jarifovi16july';        // 👈 Click 👁️ eye icon on InfinityFree to see it
    $dbname = 'if0_41771040_smart_hostel'; // ✅ Your Database Name
} else {
    // ── LOCAL SERVER (XAMPP) ────────────────────────────────
    $host   = 'localhost';
    $user   = 'root';
    $pass   = '';
    $dbname = 'smart_hostel';
}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        "status"  => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");

function jsonResponse($data) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data);
    exit;
}
?>
