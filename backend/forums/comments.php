<?php
session_start();
require_once(__DIR__ . '/../db/db.php');
$conn = DB::openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Output post data
    error_log("POST data: " . print_r($_POST, true));

    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_name = $_SESSION['user_name']; // Assuming user_name is stored in session

    // Debugging: Check session variable
    error_log("Session user_name: " . $user_name);

    $sql = "INSERT INTO comments (user_name, post_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $user_name, $post_id, $content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Comment successfully added
        header("Location: ../frontend/forums.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $conn->error;
        error_log("Error in SQL execution: " . $conn->error);
    }

    $stmt->close();
}

$conn->close();
?>
