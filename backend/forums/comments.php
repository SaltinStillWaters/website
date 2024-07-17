<?php
require_once('../backend/db/db.php');


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
            echo '<p class="text-muted" style="margin: 0;">Commented on '.$commentDateTime.'</p>';
            echo '</div></div>';
            echo '<p class="card-text mt-2">'.htmlspecialchars($comment['content']).'</p>';

            
            $currentUser = strtolower(trim($_SESSION['user_name'] ?? ''));
            $commentOwner = strtolower(trim($comment['user_name']));

            echo '<div class="dropdown ellipsis-dropdown">';
            echo '<button class="btn btn-link p-0 edit-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="fas fa-ellipsis-v"></i>';
            echo '</button>';
            echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">';
            if ($currentUser === $commentOwner) {
                
                echo '<a class="dropdown-item edit-comment" href="../backend/forums/edit_comment.php?comment_id" data-commentid="'.$comment['id'].'" data-content="'.htmlspecialchars($comment['content']).'">Edit</a>';
                echo '<a class="dropdown-item delete-comment" href="../backend/forums/delete_comment.php?comment_id='.$comment['id'].'">Delete</a>';            
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
    
    echo '<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">';
    echo '<div class="modal-dialog modal-dialog-centered">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h5 class="modal-title" id="editCommentModalLabel">Edit Comment</h5>';
    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    echo '<form id="editCommentForm">';
    echo '<div class="modal-body">';
    echo '<div class="form-group">';
    echo '<textarea class="form-control" id="editCommentContent" rows="4" placeholder="Edit your comment" required></textarea>';
    echo '</div>';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Save changes</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
?>