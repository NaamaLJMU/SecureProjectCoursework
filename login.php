<?php
include('config.php');
include('functions.php');

 session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $username = $_POST['username'];
   $password = $_POST['password'];
   if (loginUser($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 400px;
            text-align: center;
            margin-top: 20px;
        }

        h1 {
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        p {
            color: #ff0000;
        }
        nav {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }
        .forgot-pw-link{
            margin-top: 10px;
            color:black;
            text-decoration:none;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">HOME</a>
    </nav>    
   
    <form action="login.php" method="post">
        <h1>Login</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <!-- Add this inside the <form> element in login.php -->
    <a href="reset_password.php" class="forgot-pw-link">Forgot Password?</a>

</body>
</html>

