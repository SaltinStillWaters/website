<?php
session_start();
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../page_controller.php');
PageController::init(false);

$conn = DB::openConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post'])) {
        $postId = $_POST['delete_post'];
        if (deletePostById($conn, $postId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
        }
    } elseif (isset($_POST['delete_comment'])) {
        $commentId = $_POST['delete_comment'];
        if (deleteCommentById($conn, $commentId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment.']);
        }
    }
}

$conn->close();

function deletePostById($conn, $postId) {
    mysqli_begin_transaction($conn);

    try {
        $sql = "DELETE FROM comments WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            throw new Exception('mysqli prepare error: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $postId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('mysqli execute error: ' . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            throw new Exception('mysqli prepare error: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $postId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('mysqli execute error: ' . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log($e->getMessage());
        return false;
    }
}

function deleteCommentById($conn, $commentId) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        error_log('mysqli prepare error: ' . mysqli_error($conn));
        return false;
    }
    mysqli_stmt_bind_param($stmt, "i", $commentId);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        error_log('mysqli execute error: ' . mysqli_error($conn));
        mysqli_stmt_close($stmt);
        return false;
    }
}
?>
