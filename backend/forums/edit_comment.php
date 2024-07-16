<?php
require_once(__DIR__ . '/../db/db.php');
session_start();

$conn = DB::openConnection();

// Initialize message variable
$message = '';
$error = '';

// Check if the user is logged in and has the necessary permission
if (isset($_SESSION['user_name']) && isset($_POST['comment_id']) && isset($_POST['content'])) {
    $commentId = intval($_POST['comment_id']);
    $newContent = $_POST['content'];
    $userName = $_SESSION['user_name'];

    // Check if the current user is the owner of the comment
    $sql = "SELECT user_name FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $commentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $commentOwner);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($userName === $commentOwner) {
            // Update the comment
            $sqlUpdate = "UPDATE comments SET content = ? WHERE id = ?";
            $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
            if ($stmtUpdate) {
                mysqli_stmt_bind_param($stmtUpdate, "si", $newContent, $commentId);
                if (mysqli_stmt_execute($stmtUpdate)) {
                    mysqli_stmt_close($stmtUpdate);
                    $message = "Comment updated successfully";
                    $response = ['success' => true, 'message' => $message];
                    echo json_encode($response);
                    exit();
                } else {
                    $error = "Failed to update comment";
                }
            } else {
                $error = "Failed to prepare update statement";
            }
        } else {
            // User is not the owner
            $error = "Unauthorized action";
        }
    } else {
        $error = "Failed to prepare select statement";
    }
} else {
    $error = "Invalid request";
}

$response = ['success' => false, 'error' => $error];
echo json_encode($response);
exit();