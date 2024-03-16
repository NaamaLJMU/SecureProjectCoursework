<?php
include('config.php');
include('functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle the form submission for password reset
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    // Validate password strength
    $uppercase = preg_match('@[A-Z]@', $newPassword);
    $lowercase = preg_match('@[a-z]@', $newPassword);
    $number    = preg_match('@[0-9]@', $newPassword);
    $specialChars = preg_match('@[^\w]@', $newPassword);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($newPassword) < 8) {
        $error="Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";       
    }else{
        // Reset the password in the database
        resetPassword($email, $newPassword);

        // Redirect to the login page
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
    <form action="reset_password_form.php" method="post">
        <h1>Reset Password</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="email">Email:</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>" readonly>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
