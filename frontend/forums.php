<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<link href="forumSheet.css" rel="stylesheet">
    <div class="container mt-5">
        <h1 class="text-center">Discussion Forum</h1>
        <div class="row">
            <div class="col-md-8">
                <h2>Posts</h2>
                <div id="posts">
                    <?php
                    require_once(__DIR__ . '/../backend/db/db.php');
                    $conn = DB::openConnection();

                    // Function to load comments for a post
                    function loadComments($postId, $conn) {
                        $sql = "SELECT comments.content, comments.created_at, user.user_name 
                                FROM comments 
                                JOIN user ON comments.user_name = user.user_name 
                                WHERE comments.post_id = $postId 
                                ORDER BY comments.created_at ASC";
                        $result = $conn->query($sql);
                        $commentsHtml = '';

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $commentsHtml .= "<div class='comments mb-2'>
                                                    <p class='mb-1'><strong>{$row['user_name']}</strong> on {$row['created_at']}</p>
                                                    <p>{$row['content']}</p>
                                                  </div>";
                            }
                        } else {
                            $commentsHtml = "<p>No comments yet.</p>";
                        }

                        return $commentsHtml;
                    }

                    // SQL query to fetch posts with their details
                    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, u.user_name AS user_name,
                            (SELECT COUNT(*) FROM upvotes WHERE upvotes.post_id = posts.id) AS upvotes
                            FROM posts 
                            JOIN user u ON posts.user_name = u.user_name 
                            ORDER BY posts.created_at DESC";
                    $result = $conn->query($sql);

                    // Display posts
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='card mb-3'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>{$row['title']}</h5>
                                        <h6 class='card-subtitle mb-2 text-muted'>Posted by {$row['user_name']} on {$row['created_at']}</h6>
                                        <p class='card-text'>{$row['content']}</p>
                                        <button class='btn btn-success upvote' data-post-id='{$row['id']}'>Upvote ({$row['upvotes']})</button>
                                        <h6 class='mt-4'>Comments</h6>
                                        <div id='comments-{$row['id']}'>
                                            <!-- Comments will be loaded here dynamically using PHP -->
                                            ".loadComments($row['id'], $conn)."
                                        </div>
                                        <form action='../backend/forums/comments.php' method='post' class='mt-3'>
                                            <input type='hidden' name='post_id' value='{$row['id']}'>
                                            <div class='mb-3'>
                                                <textarea class='form-control' name='content' rows='2' required></textarea>
                                            </div>
                                            <button type='submit' class='btn btn-primary'>Comment</button>
                                        </form>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p>No posts available.</p>";
                    }

                    $conn->close();
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <h2>Create a Post</h2>
                <form action="../backend/forums/post.php" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upvote button functionality
        document.querySelectorAll('.upvote').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                fetch(`../backend/forums/upvotes.php?post_id=${postId}`)
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            const count = this.innerText.match(/\d+/)[0];
                            this.innerText = `Upvote (${parseInt(count) + 1})`;
                        }
                    });
            });
        });
    </script>
</body>
</html>
