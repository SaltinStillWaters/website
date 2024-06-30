<?php
session_start();
require_once(__DIR__ . '/../db/db.php');
$conn = DB::openConnection();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $user_name = $_SESSION['user_name']; // Assuming user_name is stored in session

    // Check if the user has already upvoted this post
    $sql = "SELECT * FROM upvotes WHERE post_id = ? AND user_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $post_id, $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // User hasn't upvoted this post yet, so proceed to upvote
        $sql = "INSERT INTO upvotes (user_name, post_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_name, $post_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "already_upvoted";
    }

    $stmt->close();
}

$conn->close();
?>
