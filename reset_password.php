<?php
include('config.php');
include('functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the form submission for password reset
    $email = $_POST['email'];
    
    // Generate a random verification code
    $verificationCode = generateVerificationCode();

    // Store the verification code in the database
    storeVerificationCode($email, $verificationCode);

    // Send the verification code to the user's email
    sendVerificationEmail($email, $verificationCode);

    // Redirect to a page for code verification
    header('Location: verify_code.php?email=' . urlencode($email));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
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
    </style>
</head>
<body>
    <form action="reset_password.php" method="post">
        <h1>Password Reset</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p><a href="reset_password.php">Refresh Page</a>
        <?php endif; ?>
        <label for="email">Email:</label>
        <input type="text" name="email" required>

        <button type="submit">Send Verification Code</button>
    </form>
</body>
</html>
