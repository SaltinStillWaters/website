<?php
session_start();
require_once('../backend/db/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];

        $conn = DB::openConnection();

        // Begin a transaction
        mysqli_begin_transaction($conn);

        try {
            // Delete comments related to the post
            $sqlDeleteComments = "DELETE FROM comments WHERE post_id = ?";
            $stmtComments = mysqli_prepare($conn, $sqlDeleteComments);
            if (!$stmtComments) {
                throw new Exception('Failed to prepare delete comments statement: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmtComments, "i", $post_id);
            if (!mysqli_stmt_execute($stmtComments)) {
                throw new Exception('Failed to execute delete comments statement: ' . mysqli_error($conn));
            }
            mysqli_stmt_close($stmtComments);

            // Delete the post
            $sqlDeletePost = "DELETE FROM posts WHERE id = ?";
            $stmtPost = mysqli_prepare($conn, $sqlDeletePost);
            if (!$stmtPost) {
                throw new Exception('Failed to prepare delete post statement: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmtPost, "i", $post_id);
            if (!mysqli_stmt_execute($stmtPost)) {
                throw new Exception('Failed to execute delete post statement: ' . mysqli_error($conn));
            }
            mysqli_stmt_close($stmtPost);

            // Commit transaction
            mysqli_commit($conn);

            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Post ID not set']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
