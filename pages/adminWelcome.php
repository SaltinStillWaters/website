<?php
session_start();
require_once('../backend/admin.php');
require_once('../backend/file.php');
require_once('../backend/page_controller.php');

PageController::init(false);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $_SESSION['admin'] = array_diff($_SESSION['admin'], [$_POST['remove']]);
    }

    if (isset($_POST['fileUploaded'])) {
        $canUpload = 1;

        if (count($_SESSION['admin']) == 4) {
            $error = 'News must have exactly 4 images';
            $canUpload = 0;
        }

        $targetDir = "../resources/welcome/newsTemp/";
        $targetFile = $targetDir . basename($_FILES['upload']['name']);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($canUpload && file_exists($targetFile)) {
            if (!in_array(basename($_FILES['upload']['name']), $_SESSION['admin'])) {
                $_SESSION['admin'][] = basename($_FILES['upload']['name']);
            }

            $canUpload = 0;
        }

        if ($_FILES["upload"]["size"] > 1000000) {
            $error = "File is too large. 1MB is the max";
            $canUpload = 0;
        }

        if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $canUpload = 0;
        }

        if ($canUpload && move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFile)) {
            $message = "File " . htmlspecialchars(basename($_FILES["upload"]["name"])) . " has been uploaded.";
            $_SESSION['admin'][] = basename($_FILES['upload']['name']);
        }
    }

    if (isset($_POST['save'])) {
        $canSave = 1;

        if (count($_SESSION['admin']) !== 4) {
            $error = 'News must have exactly 4 images';
            $canSave = 0;
        }

        if ($canSave) {
            $toRemove = array_diff(scandir('../resources/welcome/newsTemp'), $_SESSION['admin'], array('..', '.'));
            foreach ($toRemove as $file) {
                unlink('../resources/welcome/newsTemp/' . $file);
            }
            Files::copyFolder('../resources/welcome/newsTemp', '../resources/welcome/news');

            $message = 'changes have been saved';
        }
    }

    if (isset($_POST['discard'])) {
        header('Location: transition/toAdminWelcome.php');
        exit();
    }
    unset($_POST);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/base/base.css">
    <link rel="stylesheet" href="../css/layout/header.css">
    <link rel="stylesheet" href="../css/pages/welcome.css">
    <link href="../css/pages/adminWelcome.css" rel="stylesheet">
</head>

<body>
<header>
            <a href="#" class="logo">ml companion</a>
            <ul>
                <li><a href="transition/toAdminWelcome.php">Strategy Guides</a></li>
                <li><a href="transition/toAdminRankings.php">Hero Rankings</a></li>
                <li><a href='adminForum.php'>Forums</a></li>
                <div class="logout">                
                    <li><a href="logout.php">Log out</a></li>
                </div>
            </ul>
        </header>

    <form id='form' method="post" enctype="multipart/form-data">
        <?php

        $ctr = 0;
        foreach ($_SESSION['admin'] as $file) {
            if ($ctr === 0 || $ctr === 2) {
                echo "<div class='img-container'>";
            }

            echo "<img src='../resources/welcome/newsTemp/" . $file . "'>";
            echo "<button type='submit' class='remove' name='remove' value='$file'>x</button>";

            if ($ctr === 1 || $ctr === 3) {
                echo "</div>";
            }
            $ctr++;
        }
        echo "</div>";

        echo "<input type='file' class='upload' name='upload' value='Upload' onchange=fileUploaded()>";
        echo "<input type='submit' class='save' name='save' value='Save'>";
        echo "<input type='submit' class='discard' name='discard' value='Discard'>";
        ?>
    </form>
    <?php
    if (isset($error)) {
        echo "<div class='error'>";
        echo "<p>$error</p>";
        echo "</div>";
    }
    if (isset($message)) {
        echo "<div class='message'>";
        echo "<p>$message</p>";
        echo "</div>";
    }
    ?>
    <script>
        function fileUploaded() {
            var form = document.getElementById('form');
            var hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "fileUploaded";
            hiddenInput.value = "true";

            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
</body>

</html>