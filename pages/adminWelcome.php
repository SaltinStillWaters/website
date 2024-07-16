<?php
session_start();
require_once('../backend/admin.php');
require_once('../backend/file.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['remove']))
    {
        $_SESSION['admin'] = array_diff($_SESSION['admin'], [$_POST['remove']]);
    }

    if (isset($_POST['fileUploaded']))
    {
        $canUpload = 1;

        if (count($_SESSION['admin']) == 4)
        {
            echo 'News must have exactly 4 images';
            $canUpload = 0;
        }

        $targetDir = "../resources/welcome/newsTemp/";
        $targetFile = $targetDir . basename($_FILES['upload']['name']);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($canUpload && file_exists($targetFile)) 
        {
            if (!in_array(basename($_FILES['upload']['name']), $_SESSION['admin']))
            {
                $_SESSION['admin'][] = basename($_FILES['upload']['name']);
            }

            $canUpload = 0;
        }

        if ($_FILES["upload"]["size"] > 1000000) 
        {
            echo "File is too large. 1MB is the max";
            $canUpload = 0;
        }

        if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            $canUpload = 0;
        }
        
        if ($canUpload && move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFile)) {
            echo "File " . htmlspecialchars(basename($_FILES["upload"]["name"])) . " has been uploaded.";
            $_SESSION['admin'][] = basename($_FILES['upload']['name']);
        }
    }

    if (isset($_POST['save']))
    {
        $canSave = 1;

        if (count($_SESSION['admin']) !== 4)
        {
            echo 'News must have exactly 4 images';
            $canSave = 0;
        }

        if ($canSave)
        {
            $toRemove = array_diff(scandir('../resources/welcome/newsTemp'), $_SESSION['admin'], array('..', '.'));
            var_dump(scandir('../resources/welcome/newsTemp'));
            foreach ($toRemove as $file)
            {
                echo $file;
                unlink('../resources/welcome/newsTemp/' . $file);
            }
    
            File::copyFolder('../resources/welcome/newsTemp', '../resources/welcome/news');

            echo 'changes have been saved';
        }
    }

    if (isset($_POST['discard']))
    {
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

    <!-- <link href="../css/base/base.css" rel="stylesheet"> -->
    <link href="../css/pages/adminWelcome.css" rel="stylesheet">
</head>
<body>
    <form id='form' method="post" enctype="multipart/form-data">
    <?php
    foreach($_SESSION['admin'] as $file)
    {
        echo "<img src='../resources/welcome/newsTemp/" . $file . "'>";
        echo "<button type='submit' name='remove' value='$file'>X</button>";
    }
    echo "<input type='file' class='upload' name='upload' value='Upload' onchange=fileUploaded()>";
    echo "<input type='submit' class='save' name='save' value='Save'>";
    echo "<input type='submit' class='discard' name='discard' value='Discard'>";
    ?>
    </form>

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