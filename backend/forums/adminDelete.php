<?php
session_start();
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../page_controller.php');
PageController::init(false);

$conn = DB::openConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post'])) {
        $postId = $_POST['delete_post'];
        deletePostById($conn, $postId);
        echo json_encode(['success' => true]);
    } elseif (isset($_POST['delete_comment'])) {
        $commentId = $_POST['delete_comment'];
        deleteCommentById($conn, $commentId);
        echo json_encode(['success' => true]);
    }
}

$conn->close();

function deletePostById($conn, $postId) {
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Also delete associated comments
    deleteCommentsByPostId($conn, $postId);
}

function deleteCommentsByPostId($conn, $postId) {
    $sql = "DELETE FROM comments WHERE post_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function deleteCommentById($conn, $commentId) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $commentId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
