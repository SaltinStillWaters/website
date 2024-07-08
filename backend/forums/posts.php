<?php
require_once('../../backend/db/db.php');
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = DB::openConnection();

    $user_name = $_SESSION['user_name']; 
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Insert new post into database
    $sql = "INSERT INTO posts (user_name, title, content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $user_name, $title, $content);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../frontend/forums.php");
        exit();
    } else {
        echo "Error creating new post: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="frontend/forumSheet.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Create New Post</h1>
        <hr>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Post Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Post</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
