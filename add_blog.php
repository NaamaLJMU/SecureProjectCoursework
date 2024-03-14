<?php
include('config.php');
include('functions.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 20 mins in seconds
$inactive = 1200; 
// Check Session timeout is set or not
if(isset($_SESSION['timeout']) ) {
    $session_life = time() - $_session['timeout'];

    if($session_life > $inactive)
    {  
        session_destroy(); 
        header("Location: logout.php");     
    }
}

$_session['timeout']=time();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check=true;
    $msg="";

    // Sanitize and validate title
    $title = validateInput($_POST['title']);  
    if (!preg_match("/^[a-zA-Z ]*$/",$title)) {
        $msg = "Only letters and white space allowed";
        $check=false;
    }
    // Sanitize Blog content
    $content = validateInput($_POST['content']);

    if( $check !== false){
        $user_id = $_SESSION['user_id'];
        $image_path = handleFileUpload();
    
    
        addBlog($title, $content, $user_id, $image_path);
        header('Location: index.php'); // Redirect to the main page after adding the blog
        exit();
    }else{
        $error = $msg;
    }
}

function handleFileUpload() {
    $targetDir = "uploads/";  // Create a folder named "uploads" to store blog images
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
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

    // Return empty string if there's an error
    return 'uploads/default_blogs.png';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog</title>
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

        input,
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        textarea {
           min-height:200px;
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
    <nav>
        <a href="index.php">Home</a>
    </nav>

    <form action="add_blog.php" method="post" enctype="multipart/form-data">
        <h1>Add Blog</h1>
        <label for="title">Title:</label>
        <input type="text" name="title" required>

        <label for="content">Content:</label>
        <textarea name="content" required></textarea>

        <label for="image">Blog Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Blog</button>
    </form>
</body>
</html>


