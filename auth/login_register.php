<?php

session_start();
require_once '../config/database.php';

if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    //validation of email, password
    // $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email' ");
    // if($checkEmail->num_rows > 0 ){
    //     $_SESSION['register_error'] = "Email is already registored!";
    //     $_SESSION['active_form'] = 'register';
    // } else {
    //     $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
    // }
    // header("Location: index.php");
    // exit();
    if(empty($name) || empty($email) || empty($password) || empty($role)){
        $_SESSION['register_error'] = "Please fill in all fields.";
        $_SESSION['active_form'] = 'register';
        header("Location: ../index.php");
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['register_error'] = "Invalid email address.";
        $_SESSION['active_form'] = 'register';
        header("Location: ../index.php");
        exit();
    }
    if(strlen($password) < 8) {
        $_SESSION['register_error'] = "Password must be at least 8 characters.";
        $_SESSION['active_form'] = 'register';
        header("Location: ../index.php");
        exit();
    }

    //check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $_SESSION['register_error'] = "Email is already registered.";
        $_SESSION['active_form'] = 'register';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
        $stmt->execute();

        $_SESSION['success'] = "Registration successful. Please Login.";
        $_SESSION['active_form'] = 'login';
    }
    header("Location: ../index.php");
    exit();
}

// === Login ===
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === 'admin'){
                header("Location: ../admin/admin_page.php");
            }
            else{
                header("Location: ../user/user_page.php");
            }
            exit();
        }
    } 
    $_SESSION['login_error'] = "Incorrect email or password!";
    $_SESSION['active_form'] = 'login';
    header("Location: ../index.php");
    exit();
}
?>