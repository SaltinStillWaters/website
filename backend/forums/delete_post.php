<?php
require_once(__DIR__ . '/../db/db.php');
session_start();


if (!isset($_GET['post_id'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../../pages/forum.php");
    exit();
}


$postId = intval($_GET['post_id']);
$userName = $_SESSION['user_name'];


$conn = DB::openConnection();


$sql = "SELECT user_name FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $postOwner);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($userName === $postOwner) {

        $sql = "DELETE FROM comments WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);


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

        $_SESSION['error'] = "Unauthorized action";
    }
} else {
    $_SESSION['error'] = "Failed to prepare select statement";
}


header("Location: ../../pages/forum.php");
exit();
