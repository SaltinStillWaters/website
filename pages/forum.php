<?php
session_start();
require_once('../backend/db/db.php');

// Function to retrieve posts
function getPosts($conn) {
    $sql = "SELECT posts.*, USER.user_name FROM posts 
            INNER JOIN USER ON posts.user_name = USER.user_name 
            ORDER BY posts.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $posts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Function to retrieve comments for a post
function getComments($conn, $post_id) {
    $sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comments = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }
    }
    return $comments;
}

//Display posts and comments
function displayPosts($posts, $conn) {
    if (count($posts) > 0) {
        foreach ($posts as $post) {
            $postDateTime = date('Y-m-d H:i', strtotime($post['created_at']));
            $postUserName = strtolower(htmlspecialchars($post['user_name'] ?? 'Unknown User'));

            echo '<div class="post card mb-3 mx-auto" style="max-width: 800px;">';
            echo '<div class="card-body">';
            echo '<div class="d-flex align-items-center">';
            echo '<img src="../resources/avatar.jpg" class="avatar">';
            echo '<div class="ml-3">';
            echo '<h6 class="card-subtitle mb-2 text-muted username">'.$postUserName.'</h6>';
            echo '<p class="text-muted" style="margin: 0;">Posted on '.$postDateTime.'</p>';
            echo '</div></div>';
            echo '<h5 class="card-title mt-2">'.htmlspecialchars($post['title']).'</h5>';
            echo '<p class="card-text">'.htmlspecialchars($post['content']).'</p>';
            echo '<button class="btn btn-success" data-toggle="collapse" data-target="#comments'.$post['id'].'">Comments</button>';
            echo '</div>';

            // Display comments
            echo '<div id="comments'.$post['id'].'" class="collapse">';
            $comments = getComments($conn, $post['id']);
            if (count($comments) > 0) {
                foreach (array_reverse($comments) as $comment) {
                    $commentDateTime = date('Y-m-d H:i', strtotime($comment['created_at']));
                    $commentUserName = strtolower(htmlspecialchars($comment['user_name'] ?? 'Unknown User'));

                    echo '<div class="comment card-body border-top">';
                    echo '<div class="d-flex align-items-center">';
                    echo '<img src="../resources/avatar.jpg" class="avatar">';
                    echo '<div class="ml-3">';
                    echo '<h6 class="card-subtitle mb-2 username">'.$commentUserName.'</h6>';
                    echo '<p class="text-muted" style="margin: 0;">Posted on '.$commentDateTime.'</p>';
                    echo '</div></div>';
                    echo '<p class="card-text mt-2">'.htmlspecialchars($comment['content']).'</p>';
                    echo '</div>';
                }
            } else {
                echo '<div class="card-body border-top">';
                echo '<p class="card-text">No comments yet.</p>';
                echo '</div>';
            }

            // Comment form
            echo '<div class="card-body border-top">';
            echo '<form method="POST" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
            echo '<div class="form-group">';
            echo '<input type="hidden" name="post_id" value="'.$post['id'].'">';
            echo '<textarea class="form-control" name="comment_content" rows="2" placeholder="Add a comment" required></textarea>';
            echo '</div>';
            echo '<button type="submit" class="btn btn-primary">Comment</button>';
            echo '</form>';
            echo '</div>';
            echo '</div></div>';
        }
    } else {
        echo '<p class="text-center">No posts found.</p>';
    }
}

// Handling post submission and comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = DB::openConnection();

    // Check if comment submission
    if (isset($_POST['comment_content'])) {
        $user_name = $_SESSION['user_name'];
        $post_id = $_POST['post_id'];
        $content = $_POST['comment_content'];

        // Insert new comment into database
        $sql = "INSERT INTO comments (user_name, post_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sis", $user_name, $post_id, $content);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to current page after successful comment
            header("Location: forum.php");
            exit();
        } else {
            echo "Error adding comment: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['title']) && isset($_POST['content'])) { // Check if new post submission
        $user_name = $_SESSION['user_name'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        // Insert new post into database
        $sql = "INSERT INTO posts (user_name, title, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $user_name, $title, $content);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to forums page after successful post creation
            header("Location: forum.php");
            exit();
        } else {
            echo "Error creating new post: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}

// Retrieve and display posts
$conn = DB::openConnection();
$posts = getPosts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/base/base.css">
    <link rel="stylesheet" href="../css/layout/header.css">
    <link rel="stylesheet" href="../css/layout/footer.css">
    <link rel="stylesheet" href="../css/pages/forum.css">
</head>
<body>
    <header>
        <a href="#" class="logo">ml companion</a>
        <ul>
                <li><a href="#">Strategy Guides</a></li>
                <li><a href="rankings.php">Hero Rankings</a></li>
                <li><a href="#">Counter Picking</a></li>
                <li><a href='forum.php'>Forums</a></li>
                <div class="logout">                
                    <li><a href="logout.php">Log out</a></li>
                </div>
        </ul>
    </header>

    <div class="container mt-5">
        <h1 class="text-center">Forum</h1>
        <hr>
        <!-- Create New Post -->
        <div class="text-right mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createPostModal">
                <i class="fas fa-pencil-alt"></i> Create New Post
            </button>
        </div>
        <div class="modal fade" id="createPostModal" tabindex="-1" role="dialog" aria-labelledby="createPostModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPostModalLabel">Create New Post</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                </div>
            </div>
        </div>
        <div class="mx-auto" style="max-width: 800px;">
            <?php displayPosts($posts, $conn); ?>
        </div>
    </div>


    <footer>
        <img src="../resources/footer/app_store.png" alt="">
        <img src="../resources/footer/google_play.png" alt="">
        <img src="../resources/footer/ml_logo.png" alt="" class="logo">
    </footer>

    <script type="text/javascript">
        window.addEventListener("scroll", function() {
            var header = document.querySelector("header");
            header.classList.toggle("sticky", window.scrollY > 0);
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>

