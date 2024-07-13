<?php
session_start();
require_once(__DIR__ . '/../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'])) {
    // Open database connection
    $conn = DB::openConnection();

    // Sanitize input
    $postId = mysqli_real_escape_string($conn, $_POST['post_id']);

    // Begin a transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Delete comments associated with the post
        $deleteCommentsSql = "DELETE FROM comments WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $deleteCommentsSql);
        mysqli_stmt_bind_param($stmt, "i", $postId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error deleting comments: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        // Delete the post itself
        $deletePostSql = "DELETE FROM posts WHERE id = ?";
        $stmt = mysqli_prepare($conn, $deletePostSql);
        mysqli_stmt_bind_param($stmt, "i", $postId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error deleting post: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        // Commit the transaction
        mysqli_commit($conn);

        // Close database connection
        mysqli_close($conn);

        echo "Post and comments deleted successfully";
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on failure
        mysqli_rollback($conn);

        // Close database connection
        mysqli_close($conn);

        http_response_code(500); // Internal Server Error
        echo $e->getMessage();
        exit();
    }
} else {
    // Handle invalid requests
    http_response_code(400); // Bad Request
    echo "Invalid request";
    exit();
}
?>
