<?php
require_once('../backend/db/db.php');

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

function displayComments($conn, $post_id) {
    $comments = getComments($conn, $post_id);
    if (count($comments) > 0) {
        foreach (array_reverse($comments) as $comment) {
            $commentDateTime = date('Y-m-d H:i', strtotime($comment['created_at']));
            $commentUserName = strtolower(htmlspecialchars($comment['user_name'] ?? 'Unknown User'));

            echo '<div class="comment card-body border-top position-relative">';
            echo '<div class="d-flex align-items-center">';
            echo '<img src="../resources/avatar.jpg" class="avatar">';
            echo '<div class="ml-3">';
            echo '<h6 class="card-subtitle mb-2 username">'.$commentUserName.'</h6>';
            echo '<p class="text-muted" style="margin: 0;">Posted on '.$commentDateTime.'</p>';
            echo '</div></div>';
            echo '<p class="card-text mt-2">'.htmlspecialchars($comment['content']).'</p>';

            // Determine if current user is the comment owner
            $currentUser = strtolower(trim($_SESSION['user_name'] ?? ''));
            $commentOwner = strtolower(trim($comment['user_name']));

            echo '<div class="dropdown ellipsis-dropdown">';
            echo '<button class="btn btn-link p-0 edit-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="fas fa-ellipsis-v"></i>';
            echo '</button>';
            echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">';
            if ($currentUser === $commentOwner) {
                echo '<a class="dropdown-item" href="#">Edit</a>';
                echo '<a class="dropdown-item" href="#">Delete</a>';
            } else {
                echo '<a class="dropdown-item" href="#">Report</a>';
                echo '<a class="dropdown-item" href="#">Hide</a>';
            }
            echo '</div></div>';

            echo '</div>';
        }
    } else {
        echo '<div class="comment card-body border-top">';
        echo '<p>No comments yet.</p>';
        echo '</div>';
    }

    // Comment form
    echo '<div class="comment-form">';
    echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
    echo '<div class="form-group">';
    echo '<input type="hidden" name="post_id" value="'.$post_id.'">';
    echo '<textarea class="form-control" name="comment_content" rows="2" placeholder="Add a comment" required></textarea>';            
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Reply</button>';
    echo '</form>';
    echo '</div>';
}
?>