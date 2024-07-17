<?php
session_start();
require_once('../backend/db/db.php');
require_once('../backend/page_controller.php');
PageController::init(false);

$conn = DB::openConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is an admin
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: ../login.php');
    exit();
}

function getPosts($conn) {
    $sql = "SELECT posts.*, USER.user_name FROM posts 
            INNER JOIN USER ON posts.user_name = USER.user_name 
            ORDER BY posts.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $posts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetch comments for each post
            $row['comments'] = getComments($conn, $row['id']);
            $posts[] = $row;
        }
    }
    return $posts;
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post'])) {
        // Handle post deletion
        $postId = $_POST['delete_post'];
        deletePostById($postId); // You need to implement this function
    } elseif (isset($_POST['delete_comment'])) {
        // Handle comment deletion
        $commentId = $_POST['delete_comment'];
        deleteCommentById($commentId); // You need to implement this function
    }
}

$posts = getPosts($conn);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forum Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/base/base.css">
    <link rel="stylesheet" href="../css/layout/header.css">
    <link rel="stylesheet" href="../css/pages/welcome.css">
    <link rel="stylesheet" href="../css/pages/adminForum.css">

</head>
<body>
<header>
    <a href="#" class="logo">ml companion</a>
    <ul>
        <li><a href="welcome.php">Strategy Guides</a></li>
        <li><a href="rankings.php">Hero Rankings</a></li>
        <li><a href="#">Counter Picking</a></li>
        <li><a href='forum.php'>Forums</a></li>
        <div class="logout">                
            <li><a href="logout.php">Log out</a></li>
        </div>
    </ul>
</header>

<div class="container mt-5">
    <h1 class="container mt-5 text-center heading">Manage Forum</h1>
    <div class="mt-5 text-center">
        <button id="showPosts" class="btn btn-primary">Post Report</button>
        <button id="showComments" class="btn btn-secondary">Comment Report</button>
    </div>

    <?php
    // Sort $posts array by 'id' in ascending order
    usort($posts, function($a, $b) {
        return $a['id'] <=> $b['id'];
    });
    ?>
    <!-- Posts section -->
    <div id="postsSection" style="display:none;">
        <h3>Post Report</h3>
        <?php if (isset($_SESSION['message'])): ?>
            <div id="successMessage" class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); // Clear the message after displaying ?>
        <?php endif; ?>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['id']) ?></td>
                        <td><?= htmlspecialchars($post['user_name']) ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars($post['content']) ?></td>
                        <td><?= htmlspecialchars($post['created_at']) ?></td>
                        <td>
                            <form class="delete-form" method="post" action="../backend/forums/adminDelete.php">
                                <input type="hidden" name="delete_post" value="<?= $post['id'] ?>">
                                <button type="button" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Comments section -->
    <div id="commentsSection" style="display:none;">
        <h3>Comment Report</h3>
        <?php if (isset($_SESSION['message'])): ?>
            <div id="successMessage" class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); // Clear the message after displaying ?>
        <?php endif; ?>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Post ID</th>
                    <th>User Name</th>
                    <th>Content</th>
                    <th>Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <?php
                    // Sort comments by 'id' in ascending order
                    usort($post['comments'], function($a, $b) {
                        return $a['id'] <=> $b['id'];
                    });
                    ?>
                    <?php foreach ($post['comments'] as $comment): ?>
                        <tr>
                            <td><?= htmlspecialchars($comment['id']) ?></td>
                            <td><?= htmlspecialchars($post['id']) ?></td> <!-- Displaying post_id for reference -->
                            <td><?= htmlspecialchars($comment['user_name']) ?></td>
                            <td><?= htmlspecialchars($comment['content']) ?></td>
                            <td><?= htmlspecialchars($comment['created_at']) ?></td>
                            <td>
                                <form class="delete-form" method="post" action="../backend/forums/adminDelete.php">
                                    <input type="hidden" name="delete_comment" value="<?= $comment['id'] ?>">
                                    <button type="button" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initially hide posts section
    document.getElementById('postsSection').style.display = 'none';

    // Show comments section when "Show Comments" button is clicked
    document.getElementById('showComments').addEventListener('click', function() {
        document.getElementById('postsSection').style.display = 'none';
        document.getElementById('commentsSection').style.display = 'block';
    });

    // Show posts section when "Show Posts" button is clicked
    document.getElementById('showPosts').addEventListener('click', function() {
        document.getElementById('postsSection').style.display = 'block';
        document.getElementById('commentsSection').style.display = 'none';
    });

    // Handle delete button click
    $('.delete-form button').click(function(event) {
    if (confirm("Are you sure you want to delete this?")) {
        var form = $(this).closest('form');
        var formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    form.closest('tr').find('.alert-success').html('Deleted successfully!');
                    // Optionally remove deleted item from UI
                    form.closest('tr').remove();
                } else {
                    alert('Failed to delete.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred.');
            }
        });
    }
});
});
</script>

</body>
</html>