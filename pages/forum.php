<?php
session_start();
require_once('../backend/db/db.php');
require_once('../backend/forums/posts.php');
require_once('../backend/forums/comments.php');
require_once('../backend/page_controller.php');
PageController::init(false);

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
    } elseif (isset($_POST['title']) && isset($_POST['content'])) { 
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
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
                <li><a href="welcome.php">Strategy Guides</a></li>
                <li><a href="rankings.php">Hero Rankings</a></li>
                <li><a href='forum.php'>Forums</a></li>
                <div class="logout">                
                    <li><a href="logout.php">Log out</a></li>
                </div>
            </ul>
        </header>

    <div class="container mt-5">
        <h1 class="text-center">Forum</h1>
        <hr>
        <?php 
        if (isset($_SESSION['message'])) {
            echo '<div id="successMessage" class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Clear the message after displaying
        } 
        ?>
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

    <script>
        //handles the edit post
        document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-post');
        editButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const postId = this.dataset.postid;
                const title = this.dataset.title;
                const content = this.dataset.content;
                
                // Set modal title and content fields
                document.getElementById('editPostModalLabel').textContent = 'Edit Post';
                document.getElementById('editPostModal').dataset.postid = postId;
                document.getElementById('editTitle').value = title;
                document.getElementById('editContent').value = content;

                // Show the modal
                $('#editPostModal').modal('show');
            });
        });

        // Handle form submission inside the modal
        const editForm = document.getElementById('editForm');
        editForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const postId = document.getElementById('editPostModal').dataset.postid;
            const newTitle = document.getElementById('editTitle').value;
            const newContent = document.getElementById('editContent').value;
            
            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('title', newTitle);
            formData.append('content', newContent);

            fetch('../backend/forums/edit_post.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#editPostModal').modal('hide');
                    location.reload(); // Refresh page on success
                } else {
                    alert(data.error); // Show error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to edit post. Please try again.');
            });
        });
    });
        
    </script>


    <script> 
        //del post
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-post');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const confirmation = confirm('Are you sure you want to delete this post?');
                    if (confirmation) {
                        window.location.href = this.href;
                    }
                });
            });
        });


        //del comment
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-comment');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const confirmation = confirm('Are you sure you want to delete this comment?');
                    if (confirmation) {
                        window.location.href = this.href;
                    }
                });
            });
        });

    </script>
    
    <script>
        //edit comment
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-comment');
            editButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const commentId = this.dataset.commentid;
                    const content = this.dataset.content;
                    
                    // Set modal title and content fields
                    document.getElementById('editCommentModalLabel').textContent = 'Edit Comment';
                    document.getElementById('editCommentModal').dataset.commentid = commentId;
                    document.getElementById('editCommentContent').value = content;

                    // Show the modal
                    $('#editCommentModal').modal('show');
                });
            });

            // Handle form submission inside the modal
            const editCommentForm = document.getElementById('editCommentForm');
            editCommentForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const commentId = document.getElementById('editCommentModal').dataset.commentid;
                const newContent = document.getElementById('editCommentContent').value;
                
                const formData = new FormData();
                formData.append('comment_id', commentId);
                formData.append('content', newContent);

                fetch('../backend/forums/edit_comment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#editCommentModal').modal('hide');
                        location.reload(); // Refresh page on success (or update comments dynamically)
                    } else {
                        alert(data.error); // Show error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to edit comment. Please try again.');
                });
            });
        });
    </script>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="../backend/js/forum.js"></script>
</body>
</html>
