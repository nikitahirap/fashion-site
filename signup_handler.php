<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array();
    
    // Get POST data
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($fullname) || empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($fullname) < 3) {
        $response['success'] = false;
        $response['message'] = 'Full name must be at least 3 characters long';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($password) < 8) {
        $response['success'] = false;
        $response['message'] = 'Password must be at least 8 characters long';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = false;
            $response['message'] = 'Email already registered';
            echo json_encode($response);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$fullname, $email, $hashedPassword]);
        
        $response['success'] = true;
        $response['message'] = 'Account created successfully';
        
    } catch(PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Registration failed: ' . $e->getMessage();
    }
    
    echo json_encode($response);
}
?> 