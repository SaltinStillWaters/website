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

// Function to display posts
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
            require_once('comments.php');
            echo '<div id="comments'.$post['id'].'" class="collapse">';
            displayComments($conn, $post['id']);
            echo '</div></div>';
        }
    } else {
        echo '<p class="text-center">No posts found.</p>';
    }
}
?>
