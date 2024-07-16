<?php
require_once(__DIR__ . '/../db/db.php');
session_start();

// Check if post_id is provided via GET
if (!isset($_GET['post_id'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../../pages/forum.php");
    exit();
}

// Get the post_id from GET parameter
$postId = intval($_GET['post_id']);
$userName = $_SESSION['user_name'];

// Get the database connection
$conn = DB::openConnection();

// Check if the current user is the owner of the post
$sql = "SELECT user_name FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $postOwner);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($userName === $postOwner) {
        // User is the owner, delete the comments associated with the post first
        $sql = "DELETE FROM comments WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Now delete the post
            $sql = "DELETE FROM posts WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $postId);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    $_SESSION['message'] = "Post deleted successfully";
                } else {
                    mysqli_stmt_close($stmt);
                    $_SESSION['error'] = "Failed to delete post";
                }
            } else {
                $_SESSION['error'] = "Failed to prepare delete post statement";
            }
        } else {
            $_SESSION['error'] = "Failed to prepare delete comments statement";
        }
    } else {
        // User is not the owner
        $_SESSION['error'] = "Unauthorized action";
    }
} else {
    $_SESSION['error'] = "Failed to prepare select statement";
}

// Redirect back to the forum page
header("Location: ../../pages/forum.php");
exit();
?>
