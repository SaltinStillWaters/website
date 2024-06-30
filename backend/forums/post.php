<?php
session_start();
require_once(__DIR__ . '/../db/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    // Redirect to login or handle unauthorized access
    header("Location: ../frontend/login.php");
    exit();
}

$conn = DB::openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_name = $_SESSION['user_name']; // Assuming user_name is stored in session

    // Basic validation
    if (!empty($title) && !empty($content) && !empty($user_name)) {
        // Insert into database
        $sql = "INSERT INTO posts (user_name, title, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $user_name, $title, $content);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Post successfully created, redirect to forum page
            header("Location: ../frontend/forums.php");
            exit();
        } else {
            // Handle SQL error
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        // Handle empty fields or unauthorized access
        echo "Error: All fields are required.";
    }
}

$conn->close();
?>
