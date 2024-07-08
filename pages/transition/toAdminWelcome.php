<?php
session_start();
$_SESSION['admin'] = array_diff(scandir('../../resources/welcome/news'), array('..', '.'));
header('Location: ../adminWelcome.php');
exit();