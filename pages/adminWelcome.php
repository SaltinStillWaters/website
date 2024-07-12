<?php
session_start();
require_once('../backend/admin.php');

var_dump($_SESSION['admin']);
echo '<br><br>';
var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['remove']))
    {
        $_SESSION['admin'] = array_diff($_SESSION['admin'], [$_POST['remove']]);
    }

    if (isset($_POST['fileUploaded']))
    {
        $targetDir = "../resources/welcome/newsTemp/";
        $targetFile = $targetDir . basename($_FILES['upload']['name']);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $canUpload = 1;

        if (file_exists($targetFile)) 
        {
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