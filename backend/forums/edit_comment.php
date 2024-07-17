<?php
require_once(__DIR__ . '/../db/db.php');
session_start();

$conn = DB::openConnection();


$message = '';
$error = '';


if (isset($_SESSION['user_name']) && isset($_POST['comment_id']) && isset($_POST['content'])) {
    $commentId = intval($_POST['comment_id']);
    $newContent = $_POST['content'];
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