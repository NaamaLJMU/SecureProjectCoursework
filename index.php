<?php
include('config.php');
include('functions.php');

session_start();  // Start or resume the session

// Initialize variables
$welcomeMessage = getWelcomeMessage();
$loginLogoutButton = "";
$registerButton = "";
$addBlogButton = "";
$blogs = [];
$totalPages = 0;

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Set other buttons
    $loginLogoutButton = '<a href="logout.php" class="logout-btn">Logout</a>';
    $addBlogButton = '<a href="add_blog.php" class="add-blog-btn">Add Blog</a>';
    $registerButton = '';

} else {
    // Set other buttons for guests
    $loginLogoutButton = '<a href="login.php" class="login-btn">Login</a>';
    $addBlogButton = '';  // Don't show the "Add Blog" button for guests
    $registerButton = '<a href="register.php" class="register-btn">Register</a>';
}

// Get current page from the query string or default to 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Set the number of blogs to display per page
$perPage = 10;

// Get paginated blogs and total number of blogs
$blogs = getBlogsPaginated($page, $perPage);
$totalBlogs = getTotalBlogs();

// Pagination
$totalPages = ceil($totalBlogs / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }

        nav {
            background-color: #555;
            padding: 10px;
            text-align: center;
        }

        .logout-btn,
        .login-btn,
        .add-blog-btn,
        .register-btn {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            margin: 0 10px;
            border-radius: 5px;
        }

        .login-btn:hover,
        .logout-btn:hover,
        .add-blog-btn:hover,
        .register-btn:hover {
            background-color: #444;
        }

        .blog-list {
            display: flex;
            justify-content: center;
            margin: 20px;
            flex-wrap: wrap;
        }

        .blog-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 20px;
            width: 600px;
            max-width: 100%;
        }

        .blog-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .blog-content {
            color: #333;
        }

        .author-info {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .author-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #333;
            text-decoration: none;
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $welcomeMessage; ?></h1>
    </header>

    <nav>
        <?php echo $loginLogoutButton; ?>
        <?php echo $registerButton; ?>
        <?php echo $addBlogButton; ?>

    </nav>

    <div class="blog-list">
        <?php foreach ($blogs as $blog) : ?>
            <div class="blog-item">
                <h2><?php echo $blog['title']; ?></h2>
                <img src="<?php echo $blog['image_path']; ?>" alt="Blog Image" class="blog-image">
                <div class="blog-content">
                    <p><?php echo $blog['content']; ?></p>
                    <div class="author-info">
                        <?php if (!empty($blog['author_image'])) : ?>
                            <img src="<?php echo $blog['author_image']; ?>" alt="Author Image" class="author-image">
                        <?php endif; ?>
                        <p>Author: <?php echo $blog['full_name']; ?></p>
                    </div>
                    <p>Posted on: <?php echo $blog['created_at']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</body>
</html>
