<?php
session_start();
require_once('../../backend/file.php');
require_once('../../backend/page_controller.php');

PageController::init(false);

$_SESSION['admin'] = array_diff(scandir('../../resources/welcome/news'), array('..', '.'));


$sourceFolder = '../../resources/welcome/news';
$destinationFolder = '../../resources/welcome/newsTemp';

if (Files::copyFolder($sourceFolder, $destinationFolder)) {
    echo 'Folder cloned successfully.';
} else {
    echo 'Failed to clone folder.';
    exit();
}

PageController::setCanAccess(true, 'adminWelcome.php');
header('Location: ../adminWelcome.php');
exit();
