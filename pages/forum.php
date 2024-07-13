<?php
session_start();
require_once('../backend/db/db.php');
require_once('../backend/forums/posts.php');
require_once('../backend/forums/comments.php');

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
        <a href="welcome.php" class="logo">ml companion</a>
        <ul>
            <li><a href="#">Strategy Guides</a></li>
            <li><a href="rankings.php">Hero Rankings</a></li>
            <li><a href="#">Counter Picking</a></li>
            <li><a href="forum.php">Forums</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </header>

    <div class="container mt-5">
        <h1 class="text-center">Forum</h1>
        <hr>
        <!-- Create New Post -->
        <div class="text-right mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createPostModal">
                <i class="fas fa-pencil-alt"></i>  Create New Post
            </button>
        </div>
        <div class="modal fade" id="createPostModal" tabindex="-1" role="dialog" aria-labelledby="createPostModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
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
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post</button>
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
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle dropdown on ellipsis button click
            $('.ellipsis-dropdown').on('click', function(e) {
                e.stopPropagation();
                $(this).find('.dropdown-menu').toggleClass('show');
            });

            // Close dropdown on click outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.ellipsis-dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });
            $(document).ready(function() {
            // Handle delete post click
            $(document).on('click', '.delete-post', function(e) {
                e.preventDefault();
                if (confirm("Are you sure you want to delete this post?")) {
                    var postId = $(this).data('postid');
                    $.ajax({
                        type: 'POST',
                        url: '../backend/forums/delete_post.php', // Adjust the path based on your project structure
                        data: { post_id: postId },
                        success: function(response) {
                            // Reload the page after successful deletion
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting post:", error);
                            alert("Error deleting post. Please try again later.");
                        }
                    });
                }
            });
        });

        });
    </script>

</body>
</html>
