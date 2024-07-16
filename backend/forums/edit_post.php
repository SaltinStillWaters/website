<?php
require_once(__DIR__ . '/../db/db.php');
session_start();

// Get database connection
$conn = DB::openConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log the incoming request for debugging
    error_log(print_r($_POST, true));

    if (isset($_POST['post_id'], $_POST['title'], $_POST['content'])) {
        $postId = intval($_POST['post_id']);
        $newTitle = $_POST['title'];
        $newContent = $_POST['content'];

        // Update the post in the database
        $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $newTitle, $newContent, $postId);
            $success = mysqli_stmt_execute($stmt);

            if ($success) {
                mysqli_stmt_close($stmt);
                $response = [
                    'success' => true
                ];
                echo json_encode($response);
                exit();
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed to update post'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'error' => 'Failed to prepare statement'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'error' => 'Invalid request: Missing parameters'
        ];
    }
} else {
    $response = [
        'success' => false,
        'error' => 'Invalid request method'
    ];
}

echo json_encode($response);
?>

