<?php
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

            // Determine if current user is the post owner
            $currentUser = strtolower(trim($_SESSION['user_name'])) ?? '';
            $postOwner = strtolower(trim($post['user_name']));
            
            if ($currentUser === $postOwner) {
                // User is the owner of the post
                echo '<div class="dropdown ellipsis-dropdown">';
                echo '<button class="btn btn-link p-0 edit-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                echo '<i class="fas fa-ellipsis-v"></i>';
                echo '</button>';
                echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="options'.$post['id'].'">';
                echo '<a class="dropdown-item edit-post" data-postid="'.$post['id'].'" data-title="'.htmlspecialchars($post['title']).'" data-content="'.htmlspecialchars($post['content']).'" href="../backend/forums/edit_post.php?post_id='.$post['id'].'">Edit</a>';
                echo '<a class="dropdown-item delete-post" href="../backend/forums/delete_post.php?post_id='.$post['id'].'">Delete</a>';
                echo '</div>';
                echo '</div>';
            } else {
                // User is not the owner of the post
                echo '<div class="dropdown ellipsis-dropdown">';
                echo '<button class="btn btn-link p-0 edit-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                echo '<i class="fas fa-ellipsis-v"></i>';
                echo '</button>';
                echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="options'.$post['id'].'">';
                echo '<a class="dropdown-item report-post" data-postid="'.$post['id'].'" href="../backend/forums/report_post.php?post_id='.$post['id'].'">Report</a>';
                echo '<a class="dropdown-item hide-post" data-postid="'.$post['id'].'" href="../backend/forums/hide_post.php?post_id='.$post['id'].'">Hide</a>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '<button class="btn btn-secondary mt-2" data-toggle="collapse" data-target="#comments'.$post['id'].'">Comments</button>';
            echo '</div>';

            // Display comments
            require_once('comments.php');
            echo '<div id="comments'.$post['id'].'" class="collapse">';
            displayComments($conn, $post['id']);
            echo '</div></div>';
        }
    } else {
        echo '<p>No posts available.</p>';
    }
}

    //modal for editting posts
    echo '<div class="modal fade" id="editPostModal" tabindex="-1" role="dialog" aria-labelledby="editPostModalLabel" aria-hidden="true">';
    echo '<div class="modal-dialog modal-dialog-centered" role="document">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>';
    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<form id="editForm">';
    echo '<div class="form-group">';
    echo '<label for="editTitle">Title</label>';
    echo '<input type="text" class="form-control" id="editTitle" name="title" required>';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="editContent">Content</label>';
    echo '<textarea class="form-control" id="editContent" name="content" rows="5" required></textarea>';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Save Changes</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
?>
    