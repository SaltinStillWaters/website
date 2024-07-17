<?php
require_once(__DIR__ . '/../db/db.php');
session_start();


$conn = DB::openConnection();


$message = '';
$error = '';


if (isset($_SESSION['user_name']) && isset($_GET['comment_id'])) {
    $commentId = intval($_GET['comment_id']);
    $userName = $_SESSION['user_name'];

    
    $sql = "SELECT user_name FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $commentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $commentOwner);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($userName === $commentOwner) {
            
            $sql = "DELETE FROM comments WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $commentId);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    $message = "Comment deleted successfully";
                } else {
                    mysqli_stmt_close($stmt);
                    $error = "Failed to delete comment";
                }
            } else {
                $error = "Failed to prepare delete statement";
            }
        } else {
            
            $error = "Unauthorized action";
        }
    } else {
        $error = "Failed to prepare select statement";
    }
} else {
    $error = "Invalid request";
}


$_SESSION['message'] = $message;
$_SESSION['error'] = $error;


header("Location: ../../pages/forum.php");
exit();