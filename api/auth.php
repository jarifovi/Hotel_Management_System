<?php
require 'db.php';

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';

    if (!$name || !$email || !$password) {
        jsonResponse(["status" => "error", "message" => "All fields are required!"]);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        jsonResponse(["status" => "error", "message" => "Email already exists!"]);
    }

    // In a real app, hash password: $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
    // Sticking to plain text to match the previous localstorage logic easily, but normally DO NOT do this.
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        jsonResponse(["status" => "success", "message" => "Registration successful!"]);
    } else {
        jsonResponse(["status" => "error", "message" => "Failed to register user."]);
    }
}

if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['password'] === $password) { // Use password_verify($password, $user['password']) if hashed
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            jsonResponse([
                "status" => "success", 
                "role" => $user['role'], 
                "name" => $user['name'],
                "email" => $user['email']
            ]);
        }
    }
    jsonResponse(["status" => "error", "message" => "Invalid Email or Password!"]);
}

if ($action === 'logout') {
    session_destroy();
    jsonResponse(["status" => "success"]);
}

if ($action === 'check_session') {
    if (isset($_SESSION['user_email'])) {
        jsonResponse([
            "status" => "success", 
            "email" => $_SESSION['user_email'], 
            "name" => $_SESSION['user_name'], 
            "role" => $_SESSION['user_role']
        ]);
    } else {
        jsonResponse(["status" => "error"]);
    }
}

jsonResponse(["status" => "error", "message" => "Invalid action!"]);
?>
