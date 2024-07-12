<?php
session_start();
require_once('../../backend/file.php');

$_SESSION['admin'] = array_diff(scandir('../../resources/welcome/news'), array('..', '.'));


// Example usage:
$sourceFolder = '../../resources/welcome/news';
$destinationFolder = '../../resources/welcome/newsTemp';

if (File::copyFolder($sourceFolder, $destinationFolder)) {
    echo 'Folder cloned successfully.';
} else {
    echo 'Failed to clone folder.';
}

exit();
header('Location: ../adminWelcome.php');