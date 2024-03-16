<?php

function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getBlogsPaginated($page = 1, $perPage = 5) {
    global $conn;

    $start = ($page - 1) * $perPage;

    $stmt = $conn->prepare("
        SELECT blogs.*, users.full_name, users.image_path as author_image
        FROM blogs
        INNER JOIN users ON blogs.user_id = users.id
        ORDER BY created_at DESC
        LIMIT ?, ?
    ");
    $stmt->bind_param("ii", $start, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTotalBlogs() {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM blogs");
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['COUNT(*)'];
    $stmt->close();

    return $count;
}

function getWelcomeMessage() {
    global $conn;

    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Fetch the username from the database based on user_id
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();

        // Return the welcome message
        return "Welcome, " . ($username ?? "User");
    } else {
        // Return the welcome message for guests
        return "Welcome, Guest";
    }
}



function addBlog($title, $content, $user_id, $image_path) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, user_id, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $content, $user_id, $image_path);
    $stmt->execute();
    $stmt->close();
}

function loginUser($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($hashed_password && password_verify($password, $hashed_password)) {
        if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
            // Rehash the password and update the database
            $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Update the database with $new_hashed_password
        }
        session_start();
        $_SESSION['user_id'] = $user_id;
        return true;
    } else {
        return false;
    }
    
}


function registerUser($full_name, $username, $password, $email, $image_path) {
    global $conn;

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, image_path, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $username, $hashed_password, $image_path, $email);
    $stmt->execute();
    $stmt->close();

    return true;
}


function checkUserNameExist($username){
	global $conn;
   // Check if the username already exists
   $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? ");
   $stmt->bind_param("s", $username);
   $stmt->execute();
   $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return true;
    }else{
        return false;
    }
}

function checkEmailExist($email){
	global $conn;
    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return true;
    }else{
        return false;
    }
}

function lastSendVerification($email) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM verification WHERE email = ? AND created_at >= NOW() - INTERVAL 10 MINUTE");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();

    return $count > 0;
}

function generateVerificationCode() {
    return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

function storeVerificationCode($email, $code) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO verification (email, code) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->close();
}

function sendVerificationEmail($email, $code) {
    $subject = "Password Reset Verification Code";
    $message = "Your verification code is: $code";

    // You may need to customize the headers based on your email server requirements
    $headers = "From: test@gmail.com"; // Change this to your email address

    // Use PHP's mail function to send the email
    mail($email, $subject, $message, $headers);
}

function verifyCode($email, $code) {
    global $conn;

    $stmt = $conn->prepare("SELECT email FROM verification WHERE email = ? AND code = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();

    return $count > 0;
}

function resetPassword($email, $newPassword) {
    global $conn;

    // Hash the new password before updating the database
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();
    $stmt->close();

    // Remove the verification code from the database after successful password reset
    $stmt = $conn->prepare("DELETE FROM verification WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}

?>
