<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$dbname = 'smart_hostel';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Try selecting DB or creating it if it doesn't exist just in case
$conn->select_db($dbname);

function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
