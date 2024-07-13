<?php
require_once('../backend/db/db.php');
require_once('comments.php');

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
                echo '<a class="dropdown-item edit-post" data-postid="'.$post['id'].'" href="#">Edit</a>';
                echo '<a class="dropdown-item delete-post" data-postid="'.$post['id'].'" href="../backend/forums/delete_post.php">Delete</a>';
                echo '</div>';
                echo '</div>';
            } else {
                // User is not the owner of the post
                echo '<div class="dropdown ellipsis-dropdown">';
                echo '<button class="btn btn-link p-0 edit-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                echo '<i class="fas fa-ellipsis-v"></i>';
                echo '</button>';
                echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="options'.$post['id'].'">';
                echo '<a class="dropdown-item" href="#">Report</a>';
                echo '<a class="dropdown-item" href="#">Hide</a>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '<button class="btn btn-success mt-2" data-toggle="collapse" data-target="#comments'.$post['id'].'">Comments</button>';
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


