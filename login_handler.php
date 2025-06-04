<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array();
    
    // Get POST data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = 'Email and password are required';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Get user by email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $response['success'] = false;
            $response['message'] = 'Invalid email or password';
            echo json_encode($response);
            exit;
        }
        
        // Start session and store user data
        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'fullname' => $user['fullname'],
            'email' => $user['email']
        ];
        
        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['user'] = [
            'fullname' => $user['fullname'],
            'email' => $user['email']
        ];
        
    } catch(PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Login failed: ' . $e->getMessage();
    }
    
    echo json_encode($response);
}
?> 