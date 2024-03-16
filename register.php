<?php
include('config.php');
include('functions.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $check=true;
    $msg="";

    // Sanitize and validate name
    $full_name = validateInput($_POST['full_name']);  
    if (!preg_match("/^[a-zA-Z ]*$/",$full_name)) {
        $msg = "Only letters and white space allowed";
        $check=false;
    }

     // Sanitize and validate username
    $username = validateInput($_POST['username']);
    if (!ctype_alnum($username)) {
        $msg="Invalid username: A username contains only letters (both uppercase and lowercase) and digits (0-9).";
        $check=false;
    }

    if(checkUserNameExist($username)){
        $msg="Username already exists. Please choose a different one.";       
        $check=false;
    }
    
    //Sanitize and validate Given email
    $email = validateInput($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
        $msg="Invalid email format, please try again.";       
        $check=false;
    }

    if(checkEmailExist($email)){
        $msg="Email already exists. Please choose a different one.";       
        $check=false;
    }
     
    // Sanitize and validate Given password
    $password = validateInput($_POST['password']);
    // Validate password strength
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        $msg="Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";       
        $check=false;
    }


    if($check !== false){
        // Handle file upload for profile image
        $image_path = handleFileUpload();

        if (registerUser($full_name, $username, $password, $email, $image_path)) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Username already exists. Please choose a different one.";
        } 
    }else{
        $error = $msg;
    }
    
    
}

function handleFileUpload() {
    $targetDir = "uploads/";  // Create a folder named "uploads" to store user images
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File is not an image.";
            $uploadOk = 0;
        }
    }


    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    return 'uploads/default.png'; // Return default image path if there's an error
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    </style>
</head>
<body>
<nav>
        <a href="index.php">Home</a>
    </nav>
    <form action="register.php" method="post" enctype="multipart/form-data">
        <h1>Register</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" required>

        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="email">Email:</label>
        <input type="text" name="email" required>

        <label for="image">Profile Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Register</button>
    </form>
</body>
</html>

